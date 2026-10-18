<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_admin_redireciona_para_painel(): void
    {
        $response = $this->post('/entrar', [
            'email' => 'admin@hexavale.local',
            'password' => 'HexaVale!Admin1',
        ]);

        $response->assertRedirect('/app');
        $this->assertAuthenticated();
    }

    public function test_painel_autenticado_serve_prototipo(): void
    {
        $this->post('/entrar', [
            'email' => 'admin@hexavale.local',
            'password' => 'HexaVale!Admin1',
        ]);

        $response = $this->get('/app');
        $response->assertOk();
        $response->assertSee('HexaVale ERP Agrícola', false);
        $response->assertSee('Dashboard', false);
    }

    public function test_login_invalido_falha(): void
    {
        $response = $this->from('/entrar')->post('/entrar', [
            'email' => 'admin@hexavale.local',
            'password' => 'errada',
        ]);

        $response->assertRedirect('/entrar');
        $this->assertGuest();
    }
}
