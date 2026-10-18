<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class RbacFazendaPiiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_operador_nao_acessa_admin_usuarios(): void
    {
        $op = User::query()->where('email', 'operador@hexavale.local')->firstOrFail();
        $this->actingAs($op)->get('/admin/usuarios')->assertForbidden();
    }

    public function test_admin_lista_usuarios_com_cpf_mascarado(): void
    {
        $admin = User::query()->where('email', 'admin@hexavale.local')->firstOrFail();
        $html = $this->actingAs($admin)->get('/admin/usuarios')->assertOk()->getContent();
        $this->assertStringContainsString('***.', $html);
        $this->assertStringNotContainsString('52998224725', $html);
    }

    public function test_operador_nao_revela_cpf(): void
    {
        $op = User::query()->where('email', 'operador@hexavale.local')->firstOrFail();
        $alvo = User::query()->where('email', 'admin@hexavale.local')->firstOrFail();
        $this->actingAs($op)->post('/admin/usuarios/'.$alvo->id.'/revelar-cpf', [
            'motivo' => 'teste indevido',
        ])->assertForbidden();
    }

    public function test_admin_revela_cpf_com_motivo(): void
    {
        $admin = User::query()->where('email', 'admin@hexavale.local')->firstOrFail();
        $alvo = User::query()->where('email', 'operador@hexavale.local')->firstOrFail();
        $this->actingAs($admin)->post('/admin/usuarios/'.$alvo->id.'/revelar-cpf', [
            'motivo' => 'Auditoria interna demo',
        ])->assertRedirect();
        $this->assertDatabaseCount('acessos_pii', 1);
    }
}
