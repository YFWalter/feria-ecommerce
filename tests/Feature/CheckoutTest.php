<?php

namespace Tests\Feature;

use App\Mail\NewOrderMail;
use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    /** Arma un carrito de sesión para un producto y cantidad dados. */
    private function cartFor(Product $product, int $quantity): array
    {
        return ['product_' . $product->id => [
            'product_id' => $product->id,
            'name'       => $product->name,
            'price'      => (float) $product->price,
            'image'      => null,
            'slug'       => $product->slug,
            'stock'      => $product->stock,
            'quantity'   => $quantity,
        ]];
    }

    private function datosCliente(): array
    {
        return [
            'customer_name'    => 'Juan Pérez',
            'customer_email'   => 'juan@example.com',
            'shipping_address' => 'Calle Falsa 123',
        ];
    }

    public function test_crea_el_pedido_con_sus_items_y_descuenta_stock(): void
    {
        Mail::fake();

        $product = Product::factory()->create(['stock' => 10, 'price' => 1000]);

        $response = $this->withSession(['cart' => $this->cartFor($product, 3)])
            ->post(route('checkout.store'), $this->datosCliente());

        $order = Order::first();
        $this->assertNotNull($order);
        $response->assertRedirect(route('checkout.success', $order->number));

        $this->assertEquals(3000, $order->total);
        $this->assertTrue($order->stock_reduced);
        $this->assertDatabaseHas('order_items', [
            'order_id'   => $order->id,
            'product_id' => $product->id,
            'quantity'   => 3,
        ]);

        // El stock bajó de 10 a 7.
        $this->assertEquals(7, $product->fresh()->stock);
    }

    public function test_envia_los_emails_al_confirmar_el_pedido(): void
    {
        Mail::fake();

        $product = Product::factory()->create(['stock' => 5, 'price' => 2000]);

        $this->withSession(['cart' => $this->cartFor($product, 1)])
            ->post(route('checkout.store'), $this->datosCliente());

        Mail::assertSent(OrderConfirmationMail::class);
        Mail::assertSent(NewOrderMail::class);
    }

    public function test_rechaza_el_checkout_si_no_hay_stock_suficiente(): void
    {
        $product = Product::factory()->create(['stock' => 2]);

        $response = $this->withSession(['cart' => $this->cartFor($product, 5)])
            ->post(route('checkout.store'), $this->datosCliente());

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseCount('orders', 0);
        $this->assertEquals(2, $product->fresh()->stock); // sin cambios
    }

    public function test_redirige_al_carrito_si_esta_vacio(): void
    {
        $response = $this->post(route('checkout.store'), $this->datosCliente());

        $response->assertRedirect(route('cart.index'));
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_valida_los_datos_del_cliente(): void
    {
        $product = Product::factory()->create(['stock' => 5]);

        $response = $this->withSession(['cart' => $this->cartFor($product, 1)])
            ->post(route('checkout.store'), [
                'customer_name'  => '',
                'customer_email' => 'no-es-un-email',
            ]);

        $response->assertSessionHasErrors(['customer_name', 'customer_email', 'shipping_address']);
        $this->assertDatabaseCount('orders', 0);
    }
}
