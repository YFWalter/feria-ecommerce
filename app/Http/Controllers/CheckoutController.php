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

        // MercadoPago devuelve estos parámetros al volver de la pasarela.
        if ($request->filled('payment_id')) {
            $approved = $request->get('status') === 'approved';

            $order->update([
                'mp_payment_id'  => $request->get('payment_id'),
                'payment_status' => $approved ? 'paid' : 'pending',
                'status'         => $approved ? 'processing' : $order->status,
            ]);
        }

        // Confirmado el pedido, vaciamos el carrito.
        session()->forget('cart');

        return view('checkout.success', compact('order'));
    }

    public function pending(Request $request)
    {
        $order = $request->filled('external_reference')
            ? Order::where('number', $request->get('external_reference'))->first()
            : null;

        return view('checkout.pending', compact('order'));
    }

    public function failure(Request $request)
    {
        $order = $request->filled('external_reference')
            ? Order::where('number', $request->get('external_reference'))->first()
            : null;

        return view('checkout.failure', compact('order'));
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

        $preference = (new PreferenceClient())->create([
            'items' => $items,
            'payer' => [
                'name'  => $order->customer_name,
                'email' => $order->customer_email,
            ],
            'external_reference' => $order->number,
            'back_urls' => [
                'success' => route('checkout.success', $order->number),
                'pending' => route('checkout.pending'),
                'failure' => route('checkout.failure'),
            ],
            'auto_return'          => 'approved',
            'statement_descriptor' => 'FERIA',
        ]);

        $order->update(['mp_preference_id' => $preference->id]);

        return $preference->init_point;
    }
}
