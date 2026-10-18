<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Lote;
use App\Models\Produto;
use App\Models\User;
use HexaVale\Cadastros\Aplicacao\ExcluirProduto;
use HexaVale\Estoque\Aplicacao\RegistrarEntrada;
use HexaVale\Estoque\Aplicacao\RegistrarSaida;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class EstoqueLedgerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_entrada_aumenta_saldo_e_saida_respeita_rn07(): void
    {
        $lote = Lote::query()->where('codigo', 'AM-DEMO-01')->firstOrFail();
        $admin = User::query()->where('email', 'admin@hexavale.local')->firstOrFail();
        $fazendaId = (string) $lote->fazenda_id;

        $this->actingAs($admin);
        session(['fazenda_id' => $fazendaId]);

        /** @var RegistrarEntrada $entrada */
        $entrada = app(RegistrarEntrada::class);
        $ok = $entrada->executar($fazendaId, (string) $lote->id, '1.5', 'kg', 'NF demo', (string) $admin->id, 'idem-ent-1');
        $this->assertTrue($ok['ok']);

        /** @var RegistrarSaida $saida */
        $saida = app(RegistrarSaida::class);
        $fail = $saida->executar($fazendaId, (string) $lote->id, '9999', 'kg', 'excesso', (string) $admin->id, 'idem-sai-1');
        $this->assertFalse($fail['ok']);

        $ok2 = $saida->executar($fazendaId, (string) $lote->id, '2', 'kg', 'consumo', (string) $admin->id, 'idem-sai-2');
        $this->assertTrue($ok2['ok']);

        $this->get('/estoque/saldo')->assertOk()->assertSee('Amistar WG');
    }

    public function test_rn13_bloqueia_exclusao_produto_com_movimentacao(): void
    {
        $produto = Produto::query()->where('nome', 'Amistar WG')->firstOrFail();
        /** @var ExcluirProduto $excluir */
        $excluir = app(ExcluirProduto::class);
        $r = $excluir->executar((string) $produto->fazenda_id, (string) $produto->id, '1');
        $this->assertFalse($r['ok']);
        $this->assertStringContainsString('RN-13', (string) ($r['mensagem'] ?? ''));
    }

    public function test_idempotency_key_nao_duplica_entrada(): void
    {
        $lote = Lote::query()->where('codigo', 'CG-DEMO-07')->firstOrFail();
        $fazendaId = (string) $lote->fazenda_id;
        /** @var RegistrarEntrada $entrada */
        $entrada = app(RegistrarEntrada::class);
        $a = $entrada->executar($fazendaId, (string) $lote->id, '1', 'L', null, null, 'same-key');
        $b = $entrada->executar($fazendaId, (string) $lote->id, '1', 'L', null, null, 'same-key');
        $this->assertTrue($a['ok'] && $b['ok']);
        $this->assertSame($a['movimentacao_id'], $b['movimentacao_id']);
    }
}
