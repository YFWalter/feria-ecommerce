<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStockTest extends TestCase
{
    use RefreshDatabase;

    /** Crea un pedido con stock ya descontado para un producto. */
    private function pedidoConStockReducido(Product $product, int $quantity): Order
    {
        $order = Order::create([
            'number'           => Order::generateNumber(),
            'status'           => 'pending',
            'payment_status'   => 'pending',
            'subtotal'         => $product->price * $quantity,
            'shipping'         => 0,
            'total'            => $product->price * $quantity,
            'customer_name'    => 'Test',
            'customer_email'   => 'test@example.com',
            'shipping_address' => 'Calle 1',
            'stock_reduced'    => true,
        ]);

        $order->items()->create([
            'product_id'   => $product->id,
            'product_name' => $product->name,
            'price'        => $product->price,
            'quantity'     => $quantity,
            'subtotal'     => $product->price * $quantity,
        ]);

        // Simulamos el descuento que hace el checkout.
        $product->decrement('stock', $quantity);

        return $order;
    }

    public function test_restore_stock_devuelve_las_unidades_una_sola_vez(): void
    {
        $product = Product::factory()->create(['stock' => 5]);
        $order   = $this->pedidoConStockReducido($product, 2);

        $this->assertEquals(3, $product->fresh()->stock); // 5 - 2

        $order->restoreStock();

        $this->assertEquals(5, $product->fresh()->stock);
        $this->assertFalse($order->fresh()->stock_reduced);

        // Idempotente: una segunda llamada no vuelve a sumar.
        $order->fresh()->restoreStock();
        $this->assertEquals(5, $product->fresh()->stock);
    }

    public function test_cancelar_un_pedido_desde_el_admin_restaura_el_stock(): void
    {
        $admin   = User::factory()->admin()->create();
        $product = Product::factory()->create(['stock' => 5]);
        $order   = $this->pedidoConStockReducido($product, 2);

        $response = $this->actingAs($admin)->put(route('admin.pedidos.update', $order), [
            'status'         => 'cancelled',
            'payment_status' => 'failed',
        ]);

        $response->assertRedirect();
        $this->assertEquals(5, $product->fresh()->stock);
        $this->assertFalse($order->fresh()->stock_reduced);
    }
}
