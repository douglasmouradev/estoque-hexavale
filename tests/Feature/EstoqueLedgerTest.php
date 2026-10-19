<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Lote;
use App\Models\Produto;
use App\Models\User;
use HexaVale\Cadastros\Aplicacao\ExcluirProduto;
use HexaVale\Estoque\Aplicacao\RegistrarAjusteInventario;
use HexaVale\Estoque\Aplicacao\RegistrarEntrada;
use HexaVale\Estoque\Aplicacao\RegistrarSaida;
use HexaVale\Estoque\Aplicacao\ReservarEstoque;
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
        $ok = $entrada->executar($fazendaId, (string) $lote->id, '1.5', 'kg', 'kg', 'NF demo', (string) $admin->id, 'idem-ent-1');
        $this->assertTrue($ok['ok']);

        /** @var RegistrarSaida $saida */
        $saida = app(RegistrarSaida::class);
        $validade = $lote->validade !== null ? (string) $lote->validade : null;
        $fail = $saida->executar($fazendaId, (string) $lote->id, '9999', 'kg', 'kg', $validade, 'excesso', (string) $admin->id, 'idem-sai-1');
        $this->assertFalse($fail['ok']);

        $ok2 = $saida->executar($fazendaId, (string) $lote->id, '2', 'kg', 'kg', $validade, 'consumo', (string) $admin->id, 'idem-sai-2');
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
        $a = $entrada->executar($fazendaId, (string) $lote->id, '1', 'L', 'L', null, null, 'same-key');
        $b = $entrada->executar($fazendaId, (string) $lote->id, '1', 'L', 'L', null, null, 'same-key');
        $this->assertTrue($a['ok'] && $b['ok']);
        $this->assertSame($a['movimentacao_id'], $b['movimentacao_id']);
    }

    public function test_rn03_lote_vencido_bloqueia_saida(): void
    {
        $lote = Lote::query()->where('codigo', 'AM-DEMO-01')->firstOrFail();
        $lote->validade = '2020-01-01';
        $lote->save();

        /** @var RegistrarSaida $saida */
        $saida = app(RegistrarSaida::class);
        $r = $saida->executar(
            (string) $lote->fazenda_id,
            (string) $lote->id,
            '1',
            'kg',
            'kg',
            '2020-01-01',
            'teste',
            null,
            'sai-venc-1',
        );
        $this->assertFalse($r['ok']);
        $this->assertStringContainsString('RN-03', (string) ($r['mensagem'] ?? ''));
    }

    public function test_rn09_ajuste_exige_motivo_e_contagem_ge_reservado(): void
    {
        $lote = Lote::query()->where('codigo', 'NC-DEMO-05')->firstOrFail();
        $fazendaId = (string) $lote->fazenda_id;

        /** @var ReservarEstoque $reservar */
        $reservar = app(ReservarEstoque::class);
        $this->assertTrue($reservar->executar($fazendaId, (string) $lote->id, '100', 'kg', '2027-03-15', 'ord-1', null, 'res-1')['ok']);

        /** @var RegistrarAjusteInventario $ajuste */
        $ajuste = app(RegistrarAjusteInventario::class);
        $semMotivo = $ajuste->executar($fazendaId, (string) $lote->id, '700', 'kg', 'kg', '  ', null, 'aj-1');
        $this->assertFalse($semMotivo['ok']);

        $abaixoRes = $ajuste->executar($fazendaId, (string) $lote->id, '50', 'kg', 'kg', 'contagem baixa', null, 'aj-2');
        $this->assertFalse($abaixoRes['ok']);
        $this->assertStringContainsString('RN-09', (string) ($abaixoRes['mensagem'] ?? ''));

        $ok = $ajuste->executar($fazendaId, (string) $lote->id, '750', 'kg', 'kg', 'inventário ciclo', null, 'aj-3');
        $this->assertTrue($ok['ok']);
    }

    public function test_rn18_post_sem_idempotency_retorna_400(): void
    {
        $admin = User::query()->where('email', 'admin@hexavale.local')->firstOrFail();
        $lote = Lote::query()->firstOrFail();
        $this->actingAs($admin);
        session(['fazenda_id' => (string) $lote->fazenda_id]);

        $this->post('/estoque/entrada', [
            'lote_id' => $lote->id,
            'quantidade' => '1',
        ])->assertStatus(400);
    }

    public function test_painel_serve_prototipo_html(): void
    {
        $admin = User::query()->where('email', 'admin@hexavale.local')->firstOrFail();
        $lote = Lote::query()->firstOrFail();
        $this->actingAs($admin);
        session(['fazenda_id' => (string) $lote->fazenda_id]);

        $this->get('/app')
            ->assertOk()
            ->assertSee('HexaVale ERP Agrícola', false)
            ->assertSee('hx-btn-sair', false);
    }
}

