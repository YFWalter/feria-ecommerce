<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_el_admin_puede_crear_una_categoria(): void
    {
        $response = $this->actingAs($this->admin())->post(route('admin.categorias.store'), [
            'name'  => 'Juguetería',
            'order' => 1,
        ]);

        $response->assertRedirect(route('admin.categorias.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'Juguetería',
            'slug' => 'jugueteria',
        ]);
    }

    public function test_no_permite_eliminar_una_categoria_con_productos(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($this->admin())->delete(route('admin.categorias.destroy', $category));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_permite_eliminar_una_categoria_vacia(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin())->delete(route('admin.categorias.destroy', $category));

        $response->assertRedirect(route('admin.categorias.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
