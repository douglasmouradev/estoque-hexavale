<?php

declare(strict_types=1);

namespace HexaVale\Estoque\Aplicacao\Portas;

use HexaVale\Compartilhado\Dominio\Quantidade;
use HexaVale\Estoque\Dominio\SaldoLote;
use HexaVale\Estoque\Dominio\TipoMovimentacao;

interface PortaLedgerEstoque
{
    public function saldoDoLote(string $fazendaId, string $loteId): SaldoLote;

    /**
     * @return list<array{lote_id: string, produto_id: string, produto: string, lote: string, unidade: string, minimo: string, fisico: string, reservado: string, disponivel: string, situacao: string, validade: ?string, vencido: bool}>
     */
    public function listarSaldos(string $fazendaId): array;

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
    ): string;
}
