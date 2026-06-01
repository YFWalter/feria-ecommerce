<?php

namespace App\Http\Controllers;

use App\Mail\NewOrderMail;
use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;

class CheckoutController extends Controller
{
    /** Costo de envío fijo en pesos. 0 = a coordinar / retiro en local. */
    private const SHIPPING_COST = 0;

    public function index()
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Tu carrito está vacío.');
        }

        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $shipping = self::SHIPPING_COST;
        $total    = $subtotal + $shipping;

        return view('checkout.index', compact('cart', 'subtotal', 'shipping', 'total'));
    }

    public function store(Request $request)
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Tu carrito está vacío.');
        }

        $data = $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_email'   => 'required|email|max:255',
            'customer_phone'   => 'nullable|string|max:50',
            'shipping_address' => 'required|string|max:1000',
            'notes'            => 'nullable|string|max:1000',
        ]);

        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $shipping = self::SHIPPING_COST;
        $total    = $subtotal + $shipping;

        try {
            $order = DB::transaction(function () use ($cart, $data, $subtotal, $shipping, $total) {
                // Bloqueamos y verificamos el stock de cada producto antes de confirmar.
                foreach ($cart as $item) {
                    $product = Product::lockForUpdate()->find($item['product_id']);

                    if (! $product || ! $product->is_active) {
                        throw new \RuntimeException("El producto \"{$item['name']}\" ya no está disponible.");
                    }
                    if ($product->stock < $item['quantity']) {
                        throw new \RuntimeException("Stock insuficiente de \"{$item['name']}\" (quedan {$product->stock}).");
                    }
                }

                $order = Order::create([
                    'number'           => Order::generateNumber(),
                    'status'           => 'pending',
                    'payment_status'   => 'pending',
                    'subtotal'         => $subtotal,
                    'shipping'         => $shipping,
                    'total'            => $total,
                    'customer_name'    => $data['customer_name'],
                    'customer_email'   => $data['customer_email'],
                    'customer_phone'   => $data['customer_phone'] ?? null,
                    'shipping_address' => $data['shipping_address'],
                    'notes'            => $data['notes'] ?? null,
                    'stock_reduced'    => true,
                ]);

                foreach ($cart as $item) {
                    $order->items()->create([
                        'product_id'   => $item['product_id'],
                        'product_name' => $item['name'],
                        'price'        => $item['price'],
                        'quantity'     => $item['quantity'],
                        'subtotal'     => $item['price'] * $item['quantity'],
                    ]);

                    // Descontamos el stock vendido.
                    Product::whereKey($item['product_id'])->decrement('stock', $item['quantity']);
                }

                return $order;
            });
        } catch (\RuntimeException $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }

        // Si MercadoPago está configurado, redirigimos a la pasarela de pago.
        if (config('services.mercadopago.access_token')) {
            try {
                return redirect()->away($this->createMercadoPagoPreference($order));
            } catch (\Throwable $e) {
                Log::error('MercadoPago preference error: ' . $e->getMessage());
                // Si MP falla, conservamos el pedido y seguimos con el flujo sin pago.
            }
        }

        // Sin MercadoPago (o si falló): confirmamos el pedido con pago pendiente.
        session()->forget('cart');
        $this->sendOrderEmails($order);

        return redirect()->route('checkout.success', $order->number);
    }

    public function success(Request $request, string $number)
    {
        $order = Order::where('number', $number)->firstOrFail();

        // Confirmamos el pago consultando la API de MP (fuente confiable),
        // en lugar de confiar en los parámetros de la URL.
        $this->syncPaymentSafe($request->input('payment_id') ?? $request->input('collection_id'));
        $order->refresh();

        // Confirmado el pedido, vaciamos el carrito.
        session()->forget('cart');

        return view('checkout.success', compact('order'));
    }

    public function pending(Request $request)
    {
        $this->syncPaymentSafe($request->input('payment_id') ?? $request->input('collection_id'));

        $order = $request->filled('external_reference')
            ? Order::where('number', $request->get('external_reference'))->first()
            : null;

        return view('checkout.pending', compact('order'));
    }

    public function failure(Request $request)
    {
        $this->syncPaymentSafe($request->input('payment_id') ?? $request->input('collection_id'));

        $order = $request->filled('external_reference')
            ? Order::where('number', $request->get('external_reference'))->first()
            : null;

        return view('checkout.failure', compact('order'));
    }

    /**
     * Webhook (IPN) de MercadoPago: notificación servidor-a-servidor.
     * Es la fuente confiable del estado del pago (los back_urls dependen de que
     * el cliente vuelva al sitio). Configurar la URL en el panel de MP.
     */
    public function webhook(Request $request)
    {
        if (! config('services.mercadopago.access_token')) {
            return response()->json(['status' => 'mp disabled'], 200);
        }

        // MP manda el id del pago de distintas formas según la versión.
        $type      = $request->input('type', $request->input('topic'));
        $paymentId = $request->input('data.id', $request->input('id'));

        if ($type && $type !== 'payment') {
            return response()->json(['status' => 'ignored'], 200);
        }
        if (! $paymentId) {
            return response()->json(['status' => 'no payment id'], 200);
        }

        try {
            $this->syncPaymentById((string) $paymentId);
        } catch (\Throwable $e) {
            Log::error('MercadoPago webhook error: ' . $e->getMessage());
            // 500 → MP reintentará la notificación más tarde.
            return response()->json(['status' => 'error'], 500);
        }

        return response()->json(['status' => 'ok'], 200);
    }

    /**
     * Envía el email de confirmación al cliente y la notificación al admin.
     * No interrumpe el flujo si falla el envío (queda registrado en el log).
     */
    private function sendOrderEmails(Order $order): void
    {
        $order->loadMissing('items');

        try {
            Mail::to($order->customer_email)->send(new OrderConfirmationMail($order));

            if ($adminEmail = config('services.store.admin_email')) {
                Mail::to($adminEmail)->send(new NewOrderMail($order));
            }
        } catch (\Throwable $e) {
            Log::error("Error enviando emails del pedido {$order->number}: " . $e->getMessage());
        }
    }

    /**
     * Consulta el pago en MP y actualiza el pedido. Versión "segura" para los
     * back_urls: no lanza excepción (la página igual se muestra).
     */
    private function syncPaymentSafe(?string $paymentId): void
    {
        if (! $paymentId || ! config('services.mercadopago.access_token')) {
            return;
        }

        try {
            $this->syncPaymentById((string) $paymentId);
        } catch (\Throwable $e) {
            Log::error('MercadoPago sync error: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene el pago desde la API de MP por su id y aplica el estado al pedido
     * correspondiente (vía external_reference = número de pedido).
     */
    private function syncPaymentById(string $paymentId): void
    {
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));

        $payment = (new PaymentClient())->get($paymentId);

        if (! $payment || empty($payment->external_reference)) {
            return;
        }

        $order = Order::where('number', $payment->external_reference)->first();

        if ($order) {
            $this->applyPaymentStatus($order, $payment->status, (string) $payment->id);
        }
    }

    /**
     * Aplica el estado de pago de MP al pedido, de forma idempotente:
     * - approved  → paid + processing (envía emails una sola vez)
     * - rejected/cancelled/refunded/charged_back → failed + cancelled, restaura stock una vez
     * - pending/in_process → solo guarda el payment_id
     */
    private function applyPaymentStatus(Order $order, ?string $mpStatus, ?string $paymentId): void
    {
        $justPaid = DB::transaction(function () use ($order, $mpStatus, $paymentId) {
            // Bloqueamos la fila para evitar carreras entre el webhook y la redirección.
            $order = Order::lockForUpdate()->find($order->id);

            if (! $order) {
                return false;
            }

            if ($paymentId) {
                $order->mp_payment_id = $paymentId;
            }

            if ($mpStatus === 'approved') {
                if ($order->payment_status !== 'paid') {
                    $order->payment_status = 'paid';
                    $order->status         = 'processing';
                    $order->save();

                    return true; // disparar emails fuera de la transacción
                }

                $order->save();

                return false;
            }

            if (in_array($mpStatus, ['rejected', 'cancelled', 'refunded', 'charged_back'], true)) {
                $order->restoreStock(); // idempotente (guardado por stock_reduced)
                $order->payment_status = 'failed';
                $order->status         = 'cancelled';
                $order->save();

                return false;
            }

            // pending / in_process u otros estados intermedios.
            $order->save();

            return false;
        });

        if ($justPaid) {
            $this->sendOrderEmails($order->fresh());
        }
    }

    private function createMercadoPagoPreference(Order $order): string
    {
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));

        $items = $order->items->map(fn($item) => [
            'id'          => (string) $item->product_id,
            'title'       => $item->product_name,
            'quantity'    => (int) $item->quantity,
            'unit_price'  => (float) $item->price,
            'currency_id' => 'ARS',
        ])->all();

        if ($order->shipping > 0) {
            $items[] = [
                'title'       => 'Envío',
                'quantity'    => 1,
                'unit_price'  => (float) $order->shipping,
                'currency_id' => 'ARS',
            ];
        }

        $successUrl = route('checkout.success', $order->number);

        $preferenceData = [
            'items' => $items,
            'payer' => [
                'name'  => $order->customer_name,
                'email' => $order->customer_email,
            ],
            'external_reference' => $order->number,
            'back_urls' => [
                'success' => $successUrl,
                'pending' => route('checkout.pending'),
                'failure' => route('checkout.failure'),
            ],
            'notification_url'     => route('checkout.webhook'),
            'statement_descriptor' => 'FERIA',
        ];

        // MercadoPago rechaza auto_return si las back_urls son localhost.
        // Lo activamos solo con URL pública (producción).
        if (! Str::contains($successUrl, ['localhost', '127.0.0.1'])) {
            $preferenceData['auto_return'] = 'approved';
        }

        $preference = (new PreferenceClient())->create($preferenceData);

        $order->update(['mp_preference_id' => $preference->id]);

        return $preference->init_point;
    }
}
