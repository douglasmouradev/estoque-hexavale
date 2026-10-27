<?php

declare(strict_types=1);

namespace HexaVale\Compras\Aplicacao\Portas;

use HexaVale\Compras\Dominio\StatusPedido;

interface PortaPedidoCompra
{
    /**
     * @param  list<array{produto_id: string, quantidade: string, unidade: string}>  $itens
     */
    public function criar(
        string $fazendaId,
        ?string $fornecedorNome,
        ?string $observacao,
        string $solicitanteId,
        array $itens,
    ): string;

    /**
     * @return array{
     *   id: string,
     *   fazenda_id: string,
     *   status: string,
     *   solicitante_id: string,
     *   aprovador_id: ?string,
     *   fornecedor_nome: ?string,
     *   observacao: ?string,
     *   motivo_rejeicao: ?string,
     *   chave_nfe: ?string,
     *   itens: list<array{id: string, produto_id: string, produto: string, quantidade: string, unidade: string, unidade_base: string}>
     * }|null
     */
    public function obter(string $fazendaId, string $pedidoId): ?array;

    /**
     * @return list<array{id: string, status: string, fornecedor_nome: ?string, solicitante_id: string, criado_em: string, itens: int}>
     */
    public function listar(string $fazendaId): array;

    public function atualizarStatus(
        string $fazendaId,
        string $pedidoId,
        StatusPedido $status,
        ?string $aprovadorId = null,
        ?string $motivoRejeicao = null,
        ?string $chaveNfe = null,
    ): void;

    /**
     * Garante lote (cria se necessário) e devolve id.
     */
    public function garantirLote(
        string $fazendaId,
        string $produtoId,
        string $codigoLote,
        ?string $validadeYmd,
    ): string;
}
