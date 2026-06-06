<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'site_name'             => 'Mi Tienda',
            'theme_color'           => 'emerald',
            'home_categories_style' => 'cards',
            'hero_title'            => 'Bienvenido',
            'hero_button'           => 'Comprar',
        ], $overrides);
    }

    public function test_un_usuario_normal_no_puede_ver_la_configuracion(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.configuracion.edit'))->assertForbidden();
    }

    public function test_el_admin_puede_ver_la_configuracion(): void
    {
        $this->actingAs($this->admin())->get(route('admin.configuracion.edit'))->assertOk();
    }

    public function test_el_admin_puede_guardar_la_configuracion(): void
    {
        $response = $this->actingAs($this->admin())
            ->put(route('admin.configuracion.update'), $this->payload());

        $response->assertRedirect();
        $this->assertEquals('Mi Tienda', Setting::get('site_name'));
        $this->assertEquals('emerald', Setting::get('theme_color'));
        $this->assertEquals('Bienvenido', Setting::get('hero_title'));
    }

    public function test_rechaza_un_color_de_tema_invalido(): void
    {
        $response = $this->actingAs($this->admin())
            ->put(route('admin.configuracion.update'), $this->payload(['theme_color' => 'no-existe']));

        $response->assertSessionHasErrors('theme_color');
    }

    public function test_el_helper_setting_cae_al_default_si_no_esta_guardado(): void
    {
        // Sin nada en la tabla, devuelve el default de config/store.php
        $this->assertEquals('Feria', setting('site_name'));
        $this->assertEquals('amber', setting('theme_color'));
    }
}
