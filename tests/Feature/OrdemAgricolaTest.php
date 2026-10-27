<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Lote;
use App\Models\Talhao;
use App\Models\User;
use HexaVale\Estoque\Aplicacao\Portas\PortaLedgerEstoque;
use HexaVale\OperacaoAgricola\Aplicacao\BaixarOrdemAgricola;
use HexaVale\OperacaoAgricola\Aplicacao\CancelarOrdemAgricola;
use HexaVale\OperacaoAgricola\Aplicacao\CriarOrdemAgricola;
use HexaVale\OperacaoAgricola\Aplicacao\LiberarOrdemAgricola;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class OrdemAgricolaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_rn01_rascunho_nao_movimenta_liberar_reserva_e_cancelar_exige_motivo(): void
    {
        $admin = User::query()->where('email', 'admin@hexavale.local')->firstOrFail();
        $lote = Lote::query()->where('codigo', 'AM-DEMO-01')->firstOrFail();
        $talhao = Talhao::query()->firstOrFail();
        $fazendaId = (string) $lote->fazenda_id;
        $ledger = app(PortaLedgerEstoque::class);
        $saldoAntes = $ledger->saldoDoLote($fazendaId, (string) $lote->id);

        /** @var CriarOrdemAgricola $criar */
        $criar = app(CriarOrdemAgricola::class);
        $criado = $criar->executar(
            $fazendaId,
            (string) $talhao->id,
            'adubacao',
            '1',
            null,
            null,
            (string) $admin->id,
            [[
                'lote_id' => (string) $lote->id,
                'produto_id' => (string) $lote->produto_id,
                'dose' => '10',
                'unidade' => (string) $lote->produto?->unidade_base,
            ]],
        );
        $this->assertTrue($criado['ok'], $criado['mensagem'] ?? '');
        $ordemId = (string) $criado['ordem_id'];
        $saldoRascunho = $ledger->saldoDoLote($fazendaId, (string) $lote->id);
        $this->assertSame($saldoAntes->reservado->paraString(), $saldoRascunho->reservado->paraString());

        /** @var LiberarOrdemAgricola $liberar */
        $liberar = app(LiberarOrdemAgricola::class);
        $this->assertTrue($liberar->executar($fazendaId, $ordemId, (string) $admin->id, 'lib-1')['ok']);
        $saldoLib = $ledger->saldoDoLote($fazendaId, (string) $lote->id);
        $this->assertTrue($saldoLib->reservado->maiorQue($saldoAntes->reservado));

        /** @var CancelarOrdemAgricola $cancelar */
        $cancelar = app(CancelarOrdemAgricola::class);
        $semMotivo = $cancelar->executar($fazendaId, $ordemId, '  ', (string) $admin->id, 'can-1');
        $this->assertFalse($semMotivo['ok']);
        $this->assertStringContainsString('RN-01', (string) ($semMotivo['mensagem'] ?? ''));

        $this->assertTrue($cancelar->executar($fazendaId, $ordemId, 'chuva', (string) $admin->id, 'can-2')['ok']);
        $saldoCancel = $ledger->saldoDoLote($fazendaId, (string) $lote->id);
        $this->assertSame($saldoAntes->reservado->paraString(), $saldoCancel->reservado->paraString());
    }

    public function test_rn08_baixa_unica_e_rn06_area(): void
    {
        $admin = User::query()->where('email', 'admin@hexavale.local')->firstOrFail();
        $lote = Lote::query()->where('codigo', 'AM-DEMO-01')->firstOrFail();
        $talhao = Talhao::query()->firstOrFail();
        $fazendaId = (string) $lote->fazenda_id;

        $criado = app(CriarOrdemAgricola::class)->executar(
            $fazendaId,
            (string) $talhao->id,
            'adubacao',
            '2',
            null,
            null,
            (string) $admin->id,
            [[
                'lote_id' => (string) $lote->id,
                'produto_id' => (string) $lote->produto_id,
                'dose' => '5',
                'unidade' => (string) $lote->produto?->unidade_base,
            ]],
        );
        $ordemId = (string) $criado['ordem_id'];
        app(LiberarOrdemAgricola::class)->executar($fazendaId, $ordemId, (string) $admin->id, 'lib-b');

        /** @var BaixarOrdemAgricola $baixar */
        $baixar = app(BaixarOrdemAgricola::class);
        $areaMaior = $baixar->executar($fazendaId, $ordemId, '3', (string) $admin->id, 'bx-bad');
        $this->assertFalse($areaMaior['ok']);
        $this->assertStringContainsString('RN-06', (string) ($areaMaior['mensagem'] ?? ''));

        $ok = $baixar->executar($fazendaId, $ordemId, '2', (string) $admin->id, 'bx-1');
        $this->assertTrue($ok['ok'], $ok['mensagem'] ?? '');

        $segunda = $baixar->executar($fazendaId, $ordemId, '1', (string) $admin->id, 'bx-2');
        $this->assertFalse($segunda['ok']);
        $this->assertStringContainsString('RN-08', (string) ($segunda['mensagem'] ?? ''));
    }
}
