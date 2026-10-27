<?php

declare(strict_types=1);

namespace HexaVale\OperacaoAgricola\Aplicacao\Portas;

use HexaVale\OperacaoAgricola\Dominio\StatusOrdem;
use HexaVale\OperacaoAgricola\Dominio\TipoOrdem;

interface PortaOrdemAgricola
{
    /**
     * @param  list<array{lote_id: string, produto_id: string, dose: string, quantidade: string, unidade: string}>  $itens
     */
    public function criar(
        string $fazendaId,
        string $talhaoId,
        TipoOrdem $tipo,
        string $areaPlanejadaHa,
        ?string $caldaLHa,
        ?string $observacao,
        string $criadorId,
        array $itens,
    ): string;

    /**
     * @return array{
     *   id: string,
     *   fazenda_id: string,
     *   talhao_id: string,
     *   talhao: string,
     *   tipo: string,
     *   status: string,
     *   area_planejada_ha: string,
     *   area_tratada_ha: ?string,
     *   calda_l_ha: ?string,
     *   observacao: ?string,
     *   motivo_cancelamento: ?string,
     *   criador_id: string,
     *   itens: list<array{id: string, lote_id: string, produto_id: string, produto: string, lote: string, dose: string, quantidade: string, unidade: string, validade: ?string}>
     * }|null
     */
    public function obter(string $fazendaId, string $ordemId): ?array;

    /**
     * @return list<array{id: string, status: string, tipo: string, talhao: string, area_planejada_ha: string, criado_em: string}>
     */
    public function listar(string $fazendaId): array;

    public function atualizarStatus(
        string $fazendaId,
        string $ordemId,
        StatusOrdem $status,
        ?string $areaTratadaHa = null,
        ?string $motivoCancelamento = null,
    ): void;
}
