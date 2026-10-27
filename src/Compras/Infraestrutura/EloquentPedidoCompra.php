<?php

declare(strict_types=1);

namespace HexaVale\Compras\Infraestrutura;

use HexaVale\Compartilhado\Dominio\UuidV7;
use HexaVale\Compras\Aplicacao\Portas\PortaPedidoCompra;
use HexaVale\Compras\Dominio\StatusPedido;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

final class EloquentPedidoCompra implements PortaPedidoCompra
{
    public function criar(
        string $fazendaId,
        ?string $fornecedorNome,
        ?string $observacao,
        string $solicitanteId,
        array $itens,
    ): string {
        return DB::transaction(function () use ($fazendaId, $fornecedorNome, $observacao, $solicitanteId, $itens): string {
            $pedidoId = UuidV7::gerar()->valor;
            $agora = now();
            DB::table('pedidos_compra')->insert([
                'id' => $pedidoId,
                'fazenda_id' => $fazendaId,
                'status' => StatusPedido::Rascunho->value,
                'fornecedor_nome' => $fornecedorNome !== null && trim($fornecedorNome) !== '' ? trim($fornecedorNome) : null,
                'observacao' => $observacao,
                'solicitante_id' => $solicitanteId,
                'aprovador_id' => null,
                'motivo_rejeicao' => null,
                'chave_nfe' => null,
                'solicitado_em' => null,
                'aprovado_em' => null,
                'recebido_em' => null,
                'created_at' => $agora,
                'updated_at' => $agora,
            ]);

            foreach ($itens as $item) {
                $produtoId = (string) $item['produto_id'];
                $produto = DB::table('produtos')
                    ->where('fazenda_id', $fazendaId)
                    ->where('id', $produtoId)
                    ->whereNull('deleted_at')
                    ->first();
                if ($produto === null) {
                    throw new InvalidArgumentException('Produto inválido no pedido.');
                }
                $unidade = trim($item['unidade']);
                if ($unidade === '') {
                    $unidade = self::texto($produto->unidade_base);
                }
                DB::table('pedido_compra_itens')->insert([
                    'id' => UuidV7::gerar()->valor,
                    'pedido_id' => $pedidoId,
                    'produto_id' => $produtoId,
                    'quantidade' => $item['quantidade'],
                    'unidade' => $unidade,
                    'created_at' => $agora,
                    'updated_at' => $agora,
                ]);
            }

            return $pedidoId;
        });
    }

    public function obter(string $fazendaId, string $pedidoId): ?array
    {
        $row = DB::table('pedidos_compra')
            ->where('fazenda_id', $fazendaId)
            ->where('id', $pedidoId)
            ->first();
        if ($row === null) {
            return null;
        }

        $itens = [];
        $rowsItens = DB::table('pedido_compra_itens as i')
            ->join('produtos as p', 'p.id', '=', 'i.produto_id')
            ->where('i.pedido_id', $pedidoId)
            ->select([
                'i.id',
                'i.produto_id',
                'p.nome as produto',
                'i.quantidade',
                'i.unidade',
                'p.unidade_base',
            ])
            ->get();
        foreach ($rowsItens as $i) {
            $itens[] = [
                'id' => self::texto($i->id),
                'produto_id' => self::texto($i->produto_id),
                'produto' => self::texto($i->produto),
                'quantidade' => self::texto($i->quantidade),
                'unidade' => self::texto($i->unidade),
                'unidade_base' => self::texto($i->unidade_base),
            ];
        }

        return [
            'id' => self::texto($row->id),
            'fazenda_id' => self::texto($row->fazenda_id),
            'status' => self::texto($row->status),
            'solicitante_id' => self::texto($row->solicitante_id),
            'aprovador_id' => $row->aprovador_id !== null ? self::texto($row->aprovador_id) : null,
            'fornecedor_nome' => $row->fornecedor_nome !== null ? self::texto($row->fornecedor_nome) : null,
            'observacao' => $row->observacao !== null ? self::texto($row->observacao) : null,
            'motivo_rejeicao' => $row->motivo_rejeicao !== null ? self::texto($row->motivo_rejeicao) : null,
            'chave_nfe' => $row->chave_nfe !== null ? self::texto($row->chave_nfe) : null,
            'itens' => $itens,
        ];
    }

    public function listar(string $fazendaId): array
    {
        $rows = DB::table('pedidos_compra as p')
            ->leftJoin('pedido_compra_itens as i', 'i.pedido_id', '=', 'p.id')
            ->where('p.fazenda_id', $fazendaId)
            ->groupBy('p.id', 'p.status', 'p.fornecedor_nome', 'p.solicitante_id', 'p.created_at')
            ->orderByDesc('p.created_at')
            ->selectRaw('p.id, p.status, p.fornecedor_nome, p.solicitante_id, p.created_at, COUNT(i.id) as itens')
            ->get();

        $lista = [];
        foreach ($rows as $r) {
            $lista[] = [
                'id' => self::texto($r->id),
                'status' => self::texto($r->status),
                'fornecedor_nome' => $r->fornecedor_nome !== null ? self::texto($r->fornecedor_nome) : null,
                'solicitante_id' => self::texto($r->solicitante_id),
                'criado_em' => self::texto($r->created_at),
                'itens' => is_numeric($r->itens) ? (int) $r->itens : 0,
            ];
        }

        return $lista;
    }

    public function atualizarStatus(
        string $fazendaId,
        string $pedidoId,
        StatusPedido $status,
        ?string $aprovadorId = null,
        ?string $motivoRejeicao = null,
        ?string $chaveNfe = null,
    ): void {
        $payload = [
            'status' => $status->value,
            'updated_at' => now(),
        ];
        if ($status === StatusPedido::Solicitado) {
            $payload['solicitado_em'] = now();
        }
        if ($status === StatusPedido::Aprovado) {
            $payload['aprovador_id'] = $aprovadorId;
            $payload['aprovado_em'] = now();
            $payload['motivo_rejeicao'] = null;
        }
        if ($status === StatusPedido::Rejeitado) {
            $payload['aprovador_id'] = $aprovadorId;
            $payload['motivo_rejeicao'] = $motivoRejeicao;
            $payload['aprovado_em'] = now();
        }
        if ($status === StatusPedido::Recebido) {
            $payload['chave_nfe'] = $chaveNfe;
            $payload['recebido_em'] = now();
        }

        $n = DB::table('pedidos_compra')
            ->where('fazenda_id', $fazendaId)
            ->where('id', $pedidoId)
            ->update($payload);
        if ($n !== 1) {
            throw new RuntimeException('Falha ao atualizar status do pedido.');
        }
    }

    public function garantirLote(
        string $fazendaId,
        string $produtoId,
        string $codigoLote,
        ?string $validadeYmd,
    ): string {
        $existente = DB::table('lotes')
            ->where('fazenda_id', $fazendaId)
            ->where('produto_id', $produtoId)
            ->where('codigo', $codigoLote)
            ->whereNull('deleted_at')
            ->first();
        if ($existente !== null) {
            return self::texto($existente->id);
        }

        $id = UuidV7::gerar()->valor;
        $agora = now();
        DB::table('lotes')->insert([
            'id' => $id,
            'fazenda_id' => $fazendaId,
            'produto_id' => $produtoId,
            'codigo' => $codigoLote,
            'validade' => $validadeYmd,
            'created_at' => $agora,
            'updated_at' => $agora,
        ]);
        DB::table('saldos_lote')->insert([
            'lote_id' => $id,
            'fazenda_id' => $fazendaId,
            'fisico' => '0.000000',
            'reservado' => '0.000000',
            'atualizado_em' => $agora,
        ]);

        return $id;
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

        throw new RuntimeException('Valor inesperado ao ler pedido de compra.');
    }
}
