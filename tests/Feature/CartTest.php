<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_agrega_un_producto_con_stock_al_carrito(): void
    {
        $product = Product::factory()->create(['stock' => 10]);

        $response = $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity'   => 2,
        ]);

        $response->assertSessionHas('success');

        $cart = session('cart');
        $this->assertArrayHasKey('product_' . $product->id, $cart);
        $this->assertEquals(2, $cart['product_' . $product->id]['quantity']);
    }

    public function test_no_permite_agregar_un_producto_sin_stock(): void
    {
        $product = Product::factory()->outOfStock()->create();

        $response = $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity'   => 1,
        ]);

        $response->assertSessionHas('error');
        $this->assertArrayNotHasKey('product_' . $product->id, session('cart', []));
    }

    public function test_no_permite_agregar_un_producto_inactivo(): void
    {
        $product = Product::factory()->inactive()->create(['stock' => 10]);

        $response = $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity'   => 1,
        ]);

        $response->assertSessionHas('error');
        $this->assertArrayNotHasKey('product_' . $product->id, session('cart', []));
    }

    public function test_la_cantidad_se_limita_al_stock_disponible(): void
    {
        $product = Product::factory()->create(['stock' => 3]);

        $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity'   => 5, // más que el stock
        ]);

        $cart = session('cart');
        $this->assertEquals(3, $cart['product_' . $product->id]['quantity']);
    }
}
