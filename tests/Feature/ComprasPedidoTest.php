<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Produto;
use App\Models\User;
use HexaVale\Compras\Aplicacao\AprovarPedidoCompra;
use HexaVale\Compras\Aplicacao\CriarPedidoCompra;
use HexaVale\Compras\Aplicacao\Portas\PortaPedidoCompra;
use HexaVale\Compras\Aplicacao\ReceberPedidoCompra;
use HexaVale\Compras\Aplicacao\SolicitarAprovacaoPedido;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ComprasPedidoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_rn12_solicitante_nao_aprova_proprio_pedido(): void
    {
        $comprador = User::query()->where('email', 'comprador@hexavale.local')->firstOrFail();
        $produto = Produto::query()->firstOrFail();
        $fazendaId = (string) $produto->fazenda_id;

        /** @var CriarPedidoCompra $criar */
        $criar = app(CriarPedidoCompra::class);
        $criado = $criar->executar($fazendaId, (string) $comprador->id, 'Agro Demo', null, [
            ['produto_id' => (string) $produto->id, 'quantidade' => '10', 'unidade' => (string) $produto->unidade_base],
        ]);
        $this->assertTrue($criado['ok']);
        $pedidoId = (string) $criado['pedido_id'];

        /** @var SolicitarAprovacaoPedido $solicitar */
        $solicitar = app(SolicitarAprovacaoPedido::class);
        $this->assertTrue($solicitar->executar($fazendaId, $pedidoId, (string) $comprador->id)['ok']);

        /** @var AprovarPedidoCompra $aprovar */
        $aprovar = app(AprovarPedidoCompra::class);
        $auto = $aprovar->executar($fazendaId, $pedidoId, (string) $comprador->id);
        $this->assertFalse($auto['ok']);
        $this->assertStringContainsString('RN-12', (string) ($auto['mensagem'] ?? ''));

        $aprovador = User::query()->where('email', 'aprovador@hexavale.local')->firstOrFail();
        $ok = $aprovar->executar($fazendaId, $pedidoId, (string) $aprovador->id);
        $this->assertTrue($ok['ok']);
    }

    public function test_receber_com_chave_nfe_gera_entrada(): void
    {
        $comprador = User::query()->where('email', 'comprador@hexavale.local')->firstOrFail();
        $aprovador = User::query()->where('email', 'aprovador@hexavale.local')->firstOrFail();
        $admin = User::query()->where('email', 'admin@hexavale.local')->firstOrFail();
        $produto = Produto::query()->firstOrFail();
        $fazendaId = (string) $produto->fazenda_id;

        /** @var CriarPedidoCompra $criar */
        $criar = app(CriarPedidoCompra::class);
        $criado = $criar->executar($fazendaId, (string) $comprador->id, 'Fornecedor X', null, [
            ['produto_id' => (string) $produto->id, 'quantidade' => '5', 'unidade' => (string) $produto->unidade_base],
        ]);
        $pedidoId = (string) $criado['pedido_id'];

        app(SolicitarAprovacaoPedido::class)->executar($fazendaId, $pedidoId, (string) $comprador->id);
        app(AprovarPedidoCompra::class)->executar($fazendaId, $pedidoId, (string) $aprovador->id);

        $pedido = app(PortaPedidoCompra::class)->obter($fazendaId, $pedidoId);
        $this->assertNotNull($pedido);
        $itemId = $pedido['itens'][0]['id'];

        /** @var ReceberPedidoCompra $receber */
        $receber = app(ReceberPedidoCompra::class);
        $r = $receber->executar(
            $fazendaId,
            $pedidoId,
            '35240112345678901234567890123456789012345678',
            [['item_id' => $itemId, 'lote_codigo' => 'NF-LOTE-01', 'validade' => '2027-12-31']],
            (string) $admin->id,
            'recv-1',
        );
        $this->assertTrue($r['ok'], $r['mensagem'] ?? '');

        $this->actingAs($admin);
        session(['fazenda_id' => $fazendaId]);
        $this->get('/compras/'.$pedidoId)->assertOk()->assertSee('recebido', false);
    }

    public function test_http_comprador_cria_pedido(): void
    {
        $comprador = User::query()->where('email', 'comprador@hexavale.local')->firstOrFail();
        $produto = Produto::query()->firstOrFail();
        $this->actingAs($comprador);
        session(['fazenda_id' => (string) $produto->fazenda_id]);

        $this->get('/compras/novo')->assertOk();
        $this->post('/compras', [
            'fornecedor_nome' => 'Casa Agro',
            'produto_id' => [(string) $produto->id],
            'quantidade' => ['2'],
            'unidade' => [(string) $produto->unidade_base],
        ])->assertRedirect();
    }
}
