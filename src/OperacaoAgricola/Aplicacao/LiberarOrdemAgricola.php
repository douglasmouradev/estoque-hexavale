<?php

declare(strict_types=1);

namespace HexaVale\OperacaoAgricola\Aplicacao;

use HexaVale\Auditoria\Aplicacao\PortaRegistrarAuditoria;
use HexaVale\Estoque\Aplicacao\ReservarEstoque;
use HexaVale\OperacaoAgricola\Aplicacao\Portas\PortaOrdemAgricola;
use HexaVale\OperacaoAgricola\Dominio\StatusOrdem;

final readonly class LiberarOrdemAgricola
{
    public function __construct(
        private PortaOrdemAgricola $ordens,
        private ReservarEstoque $reservar,
        private PortaRegistrarAuditoria $auditoria,
    ) {}

    /**
     * @return array{ok: bool, mensagem?: string}
     */
    public function executar(
        string $fazendaId,
        string $ordemId,
        string $atorId,
        string $idempotencyPrefix,
    ): array {
        $ordem = $this->ordens->obter($fazendaId, $ordemId);
        if ($ordem === null) {
            return ['ok' => false, 'mensagem' => 'Ordem não encontrada.'];
        }
        if ($ordem['status'] !== StatusOrdem::Rascunho->value) {
            return ['ok' => false, 'mensagem' => 'Só rascunho pode ser liberado (RN-01).'];
        }
        if ($ordem['itens'] === []) {
            return ['ok' => false, 'mensagem' => 'Ordem sem itens.'];
        }

        foreach ($ordem['itens'] as $item) {
            $r = $this->reservar->executar(
                $fazendaId,
                $item['lote_id'],
                $item['quantidade'],
                $item['unidade'],
                $item['validade'],
                $ordemId,
                $atorId,
                $idempotencyPrefix.'-'.$item['id'],
            );
            if (($r['ok'] ?? false) !== true) {
                return ['ok' => false, 'mensagem' => $r['mensagem'] ?? 'Falha ao reservar item.'];
            }
        }

        $this->ordens->atualizarStatus($fazendaId, $ordemId, StatusOrdem::Liberada);
        $this->auditoria->registrar(
            $fazendaId,
            $atorId,
            'ORDEM_LIBERADA',
            'ordem_agricola',
            $ordemId,
            ['status' => StatusOrdem::Rascunho->value],
            ['status' => StatusOrdem::Liberada->value],
            null,
            null,
            null,
        );

        return ['ok' => true];
    }
}
