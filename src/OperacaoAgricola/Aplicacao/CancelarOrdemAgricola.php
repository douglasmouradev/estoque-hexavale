<?php

declare(strict_types=1);

namespace HexaVale\OperacaoAgricola\Aplicacao;

use HexaVale\Auditoria\Aplicacao\PortaRegistrarAuditoria;
use HexaVale\Estoque\Aplicacao\LiberarReserva;
use HexaVale\OperacaoAgricola\Aplicacao\Portas\PortaOrdemAgricola;
use HexaVale\OperacaoAgricola\Dominio\StatusOrdem;

final readonly class CancelarOrdemAgricola
{
    public function __construct(
        private PortaOrdemAgricola $ordens,
        private LiberarReserva $liberar,
        private PortaRegistrarAuditoria $auditoria,
    ) {}

    /**
     * RN-01 — cancelar exige motivo; se liberada, devolve reserva.
     *
     * @return array{ok: bool, mensagem?: string}
     */
    public function executar(
        string $fazendaId,
        string $ordemId,
        string $motivo,
        string $atorId,
        string $idempotencyPrefix,
    ): array {
        $motivo = trim($motivo);
        if ($motivo === '') {
            return ['ok' => false, 'mensagem' => 'Cancelamento exige motivo (RN-01).'];
        }
        $ordem = $this->ordens->obter($fazendaId, $ordemId);
        if ($ordem === null) {
            return ['ok' => false, 'mensagem' => 'Ordem não encontrada.'];
        }
        if ($ordem['status'] === StatusOrdem::Baixada->value || $ordem['status'] === StatusOrdem::Cancelada->value) {
            return ['ok' => false, 'mensagem' => 'Ordem não pode ser cancelada neste status.'];
        }

        if ($ordem['status'] === StatusOrdem::Liberada->value) {
            foreach ($ordem['itens'] as $item) {
                $r = $this->liberar->executar(
                    $fazendaId,
                    $item['lote_id'],
                    $item['quantidade'],
                    $item['unidade'],
                    $ordemId,
                    $atorId,
                    $idempotencyPrefix.'-'.$item['id'],
                );
                if (($r['ok'] ?? false) !== true) {
                    return ['ok' => false, 'mensagem' => $r['mensagem'] ?? 'Falha ao liberar reserva.'];
                }
            }
        }

        $antes = $ordem['status'];
        $this->ordens->atualizarStatus($fazendaId, $ordemId, StatusOrdem::Cancelada, null, $motivo);
        $this->auditoria->registrar(
            $fazendaId,
            $atorId,
            'ORDEM_CANCELADA',
            'ordem_agricola',
            $ordemId,
            ['status' => $antes],
            ['status' => StatusOrdem::Cancelada->value],
            $motivo,
            null,
            null,
        );

        return ['ok' => true];
    }
}
