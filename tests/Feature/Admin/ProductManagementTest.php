<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_el_admin_puede_crear_un_producto(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin())->post(route('admin.productos.store'), [
            'category_id' => $category->id,
            'name'        => 'Producto de Prueba',
            'price'       => 1500,
            'stock'       => 8,
        ]);

        $response->assertRedirect(route('admin.productos.index'));
        $this->assertDatabaseHas('products', [
            'name' => 'Producto de Prueba',
            'slug' => 'producto-de-prueba',
        ]);
    }

    public function test_el_admin_puede_editar_un_producto(): void
    {
        $product = Product::factory()->create(['name' => 'Viejo', 'price' => 1000]);

        $response = $this->actingAs($this->admin())->put(route('admin.productos.update', $product), [
            'category_id' => $product->category_id,
            'name'        => 'Nuevo Nombre',
            'price'       => 2500,
            'stock'       => $product->stock,
        ]);

        $response->assertRedirect(route('admin.productos.index'));
        $this->assertDatabaseHas('products', [
            'id'    => $product->id,
            'name'  => 'Nuevo Nombre',
            'price' => 2500,
        ]);
    }

    public function test_el_admin_puede_eliminar_un_producto(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin())->delete(route('admin.productos.destroy', $product));

        $response->assertRedirect(route('admin.productos.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_la_creacion_valida_campos_obligatorios(): void
    {
        $response = $this->actingAs($this->admin())->post(route('admin.productos.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors(['category_id', 'name', 'price', 'stock']);
    }
}
