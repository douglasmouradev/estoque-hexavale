<?php

declare(strict_types=1);

namespace HexaVale\OperacaoAgricola\Infraestrutura;

use HexaVale\Compartilhado\Dominio\UuidV7;
use HexaVale\OperacaoAgricola\Aplicacao\Portas\PortaOrdemAgricola;
use HexaVale\OperacaoAgricola\Dominio\StatusOrdem;
use HexaVale\OperacaoAgricola\Dominio\TipoOrdem;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

final class EloquentOrdemAgricola implements PortaOrdemAgricola
{
    public function criar(
        string $fazendaId,
        string $talhaoId,
        TipoOrdem $tipo,
        string $areaPlanejadaHa,
        ?string $caldaLHa,
        ?string $observacao,
        string $criadorId,
        array $itens,
    ): string {
        return DB::transaction(function () use ($fazendaId, $talhaoId, $tipo, $areaPlanejadaHa, $caldaLHa, $observacao, $criadorId, $itens): string {
            $talhao = DB::table('talhoes')
                ->where('fazenda_id', $fazendaId)
                ->where('id', $talhaoId)
                ->whereNull('deleted_at')
                ->first();
            if ($talhao === null) {
                throw new InvalidArgumentException('Talhão inválido.');
            }

            $ordemId = UuidV7::gerar()->valor;
            $agora = now();
            DB::table('ordens_agricolas')->insert([
                'id' => $ordemId,
                'fazenda_id' => $fazendaId,
                'talhao_id' => $talhaoId,
                'tipo' => $tipo->value,
                'status' => StatusOrdem::Rascunho->value,
                'area_planejada_ha' => $areaPlanejadaHa,
                'area_tratada_ha' => null,
                'calda_l_ha' => $caldaLHa !== null && $caldaLHa !== '' ? $caldaLHa : null,
                'observacao' => $observacao,
                'motivo_cancelamento' => null,
                'criador_id' => $criadorId,
                'liberada_em' => null,
                'baixada_em' => null,
                'cancelada_em' => null,
                'created_at' => $agora,
                'updated_at' => $agora,
            ]);

            foreach ($itens as $item) {
                $lote = DB::table('lotes')
                    ->where('fazenda_id', $fazendaId)
                    ->where('id', $item['lote_id'])
                    ->whereNull('deleted_at')
                    ->first();
                if ($lote === null) {
                    throw new InvalidArgumentException('Lote inválido na ordem.');
                }
                DB::table('ordem_agricola_itens')->insert([
                    'id' => UuidV7::gerar()->valor,
                    'ordem_id' => $ordemId,
                    'lote_id' => $item['lote_id'],
                    'produto_id' => $item['produto_id'],
                    'dose' => $item['dose'],
                    'quantidade' => $item['quantidade'],
                    'unidade' => $item['unidade'],
                    'created_at' => $agora,
                    'updated_at' => $agora,
                ]);
            }

            return $ordemId;
        });
    }

    public function obter(string $fazendaId, string $ordemId): ?array
    {
        $row = DB::table('ordens_agricolas as o')
            ->join('talhoes as t', 't.id', '=', 'o.talhao_id')
            ->where('o.fazenda_id', $fazendaId)
            ->where('o.id', $ordemId)
            ->select(['o.*', 't.codigo as talhao'])
            ->first();
        if ($row === null) {
            return null;
        }

        $itens = [];
        $rows = DB::table('ordem_agricola_itens as i')
            ->join('produtos as p', 'p.id', '=', 'i.produto_id')
            ->join('lotes as l', 'l.id', '=', 'i.lote_id')
            ->where('i.ordem_id', $ordemId)
            ->select([
                'i.id', 'i.lote_id', 'i.produto_id', 'p.nome as produto', 'l.codigo as lote',
                'i.dose', 'i.quantidade', 'i.unidade', 'l.validade',
            ])
            ->get();
        foreach ($rows as $i) {
            $itens[] = [
                'id' => self::texto($i->id),
                'lote_id' => self::texto($i->lote_id),
                'produto_id' => self::texto($i->produto_id),
                'produto' => self::texto($i->produto),
                'lote' => self::texto($i->lote),
                'dose' => self::texto($i->dose),
                'quantidade' => self::texto($i->quantidade),
                'unidade' => self::texto($i->unidade),
                'validade' => $i->validade !== null ? self::texto($i->validade) : null,
            ];
        }

        return [
            'id' => self::texto($row->id),
            'fazenda_id' => self::texto($row->fazenda_id),
            'talhao_id' => self::texto($row->talhao_id),
            'talhao' => self::texto($row->talhao),
            'tipo' => self::texto($row->tipo),
            'status' => self::texto($row->status),
            'area_planejada_ha' => self::texto($row->area_planejada_ha),
            'area_tratada_ha' => $row->area_tratada_ha !== null ? self::texto($row->area_tratada_ha) : null,
            'calda_l_ha' => $row->calda_l_ha !== null ? self::texto($row->calda_l_ha) : null,
            'observacao' => $row->observacao !== null ? self::texto($row->observacao) : null,
            'motivo_cancelamento' => $row->motivo_cancelamento !== null ? self::texto($row->motivo_cancelamento) : null,
            'criador_id' => self::texto($row->criador_id),
            'itens' => $itens,
        ];
    }

    public function listar(string $fazendaId): array
    {
        $rows = DB::table('ordens_agricolas as o')
            ->join('talhoes as t', 't.id', '=', 'o.talhao_id')
            ->where('o.fazenda_id', $fazendaId)
            ->orderByDesc('o.created_at')
            ->select(['o.id', 'o.status', 'o.tipo', 't.codigo as talhao', 'o.area_planejada_ha', 'o.created_at'])
            ->get();

        $lista = [];
        foreach ($rows as $r) {
            $lista[] = [
                'id' => self::texto($r->id),
                'status' => self::texto($r->status),
                'tipo' => self::texto($r->tipo),
                'talhao' => self::texto($r->talhao),
                'area_planejada_ha' => self::texto($r->area_planejada_ha),
                'criado_em' => self::texto($r->created_at),
            ];
        }

        return $lista;
    }

    public function atualizarStatus(
        string $fazendaId,
        string $ordemId,
        StatusOrdem $status,
        ?string $areaTratadaHa = null,
        ?string $motivoCancelamento = null,
    ): void {
        $payload = [
            'status' => $status->value,
            'updated_at' => now(),
        ];
        if ($status === StatusOrdem::Liberada) {
            $payload['liberada_em'] = now();
        }
        if ($status === StatusOrdem::Baixada) {
            $payload['area_tratada_ha'] = $areaTratadaHa;
            $payload['baixada_em'] = now();
        }
        if ($status === StatusOrdem::Cancelada) {
            $payload['motivo_cancelamento'] = $motivoCancelamento;
            $payload['cancelada_em'] = now();
        }
        $n = DB::table('ordens_agricolas')
            ->where('fazenda_id', $fazendaId)
            ->where('id', $ordemId)
            ->update($payload);
        if ($n !== 1) {
            throw new RuntimeException('Falha ao atualizar status da ordem.');
        }
    }

    private static function texto(mixed $valor): string
    {
        if (is_string($valor)) {
            return $valor;
        }
        if (is_int($valor) || is_float($valor)) {
            return (string) $valor;
        }
        if ($valor instanceof \Stringable) {
            return (string) $valor;
        }

        throw new RuntimeException('Valor inesperado ao ler ordem agrícola.');
    }
}
