<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_invitado_es_redirigido_al_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_un_usuario_normal_no_puede_entrar_al_admin(): void
    {
        $user = User::factory()->create(); // is_admin = false

        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_un_admin_puede_entrar_al_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
    }
}
