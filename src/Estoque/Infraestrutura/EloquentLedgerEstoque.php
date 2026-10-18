<?php

declare(strict_types=1);

namespace HexaVale\Estoque\Infraestrutura;

use App\Models\Lote;
use App\Models\Movimentacao;
use App\Models\SaldoLote as SaldoLoteModel;
use HexaVale\Compartilhado\Dominio\Quantidade;
use HexaVale\Compartilhado\Dominio\UuidV7;
use HexaVale\Estoque\Aplicacao\Portas\PortaLedgerEstoque;
use HexaVale\Estoque\Dominio\SaldoLote;
use HexaVale\Estoque\Dominio\TipoMovimentacao;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

final class EloquentLedgerEstoque implements PortaLedgerEstoque
{
    public function saldoDoLote(string $fazendaId, string $loteId): SaldoLote
    {
        $row = SaldoLoteModel::query()
            ->where('fazenda_id', $fazendaId)
            ->where('lote_id', $loteId)
            ->first();

        if ($row === null) {
            return new SaldoLote(Quantidade::zero(), Quantidade::zero());
        }

        return new SaldoLote(
            Quantidade::de((string) $row->fisico),
            Quantidade::de((string) $row->reservado),
        );
    }

    public function listarSaldos(string $fazendaId): array
    {
        $lotes = Lote::query()
            ->with(['produto', 'saldo'])
            ->where('fazenda_id', $fazendaId)
            ->orderBy('codigo')
            ->get();

        $out = [];
        foreach ($lotes as $lote) {
            $produto = $lote->produto;
            if ($produto === null) {
                continue;
            }
            $saldoRow = $lote->saldo;
            $fisico = Quantidade::de((string) ($saldoRow !== null ? $saldoRow->fisico : '0'));
            $reservado = Quantidade::de((string) ($saldoRow !== null ? $saldoRow->reservado : '0'));
            $saldo = new SaldoLote($fisico, $reservado);
            $minimo = Quantidade::de((string) $produto->minimo);
            $situacao = $saldo->situacao($minimo);
            $out[] = [
                'lote_id' => (string) $lote->id,
                'produto_id' => (string) $produto->id,
                'produto' => (string) $produto->nome,
                'lote' => (string) $lote->codigo,
                'unidade' => (string) $produto->unidade_base,
                'minimo' => $minimo->paraString(),
                'fisico' => $fisico->paraString(),
                'reservado' => $reservado->paraString(),
                'disponivel' => $saldo->disponivel()->paraString(),
                'situacao' => $situacao->value,
                'validade' => $lote->validade !== null ? (string) $lote->validade : null,
            ];
        }

        return $out;
    }

    public function registrar(
        string $fazendaId,
        string $loteId,
        TipoMovimentacao $tipo,
        Quantidade $quantidade,
        string $unidade,
        ?string $motivo,
        ?string $atorId,
        ?string $idempotencyKey,
        ?string $referenciaTipo = null,
        ?string $referenciaId = null,
    ): string {
        if ($idempotencyKey !== null && $idempotencyKey !== '') {
            $existente = Movimentacao::query()
                ->where('fazenda_id', $fazendaId)
                ->where('idempotency_key', $idempotencyKey)
                ->first();
            if ($existente !== null) {
                return (string) $existente->id;
            }
        }

        $lote = Lote::query()->where('fazenda_id', $fazendaId)->where('id', $loteId)->first();
        if ($lote === null) {
            throw new InvalidArgumentException('Lote não encontrado nesta fazenda.');
        }

        return DB::transaction(function () use (
            $fazendaId, $loteId, $tipo, $quantidade, $unidade, $motivo,
            $atorId, $idempotencyKey, $referenciaTipo, $referenciaId
        ): string {
            $atual = $this->saldoDoLote($fazendaId, $loteId);
            $novo = match ($tipo) {
                TipoMovimentacao::Entrada, TipoMovimentacao::AjustePos => $atual->aplicarEntrada($quantidade),
                TipoMovimentacao::Saida, TipoMovimentacao::AjusteNeg => $atual->aplicarSaida($quantidade),
                TipoMovimentacao::Reserva, TipoMovimentacao::BaixaReserva => throw new RuntimeException(
                    'Reserva/baixa de reserva será implementada com ordens (Fase 5).',
                ),
            };

            $id = UuidV7::gerar()->valor;
            Movimentacao::query()->create([
                'id' => $id,
                'fazenda_id' => $fazendaId,
                'lote_id' => $loteId,
                'tipo' => $tipo->value,
                'quantidade' => $quantidade->paraString(),
                'unidade' => $unidade,
                'motivo' => $motivo,
                'referencia_tipo' => $referenciaTipo,
                'referencia_id' => $referenciaId,
                'ator_id' => $atorId,
                'idempotency_key' => $idempotencyKey,
                'ocorreu_em' => now(),
            ]);

            SaldoLoteModel::query()->updateOrCreate(
                ['lote_id' => $loteId],
                [
                    'fazenda_id' => $fazendaId,
                    'fisico' => $novo->fisico->paraString(),
                    'reservado' => $novo->reservado->paraString(),
                    'atualizado_em' => now(),
                ],
            );

            return $id;
        });
    }
}
