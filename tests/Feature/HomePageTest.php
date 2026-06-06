<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_home_carga_correctamente(): void
    {
        $this->get(route('home'))->assertOk();
    }

    public function test_solo_muestra_categorias_marcadas_para_la_home(): void
    {
        Category::factory()->create(['name' => 'CategoriaVisible', 'show_on_home' => true]);
        Category::factory()->create(['name' => 'CategoriaOculta', 'show_on_home' => false]);

        $response = $this->get(route('home'));

        $response->assertSee('CategoriaVisible');
        $response->assertDontSee('CategoriaOculta');
    }

    public function test_respeta_el_limite_de_categorias_de_la_home(): void
    {
        Category::factory()->count(5)->create(['show_on_home' => true]);
        Setting::set('home_categories_limit', 2);

        // El controlador limita la cantidad de categorías pasadas a la vista.
        $response = $this->get(route('home'));
        $response->assertOk();

        $categories = $response->viewData('categories');
        $this->assertCount(2, $categories);
    }
}
