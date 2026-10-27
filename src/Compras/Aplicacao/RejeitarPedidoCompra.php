<?php

declare(strict_types=1);

namespace HexaVale\Compras\Aplicacao;

use HexaVale\Auditoria\Aplicacao\PortaRegistrarAuditoria;
use HexaVale\Compras\Aplicacao\Portas\PortaPedidoCompra;
use HexaVale\Compras\Dominio\PoliticaAprovacao;
use HexaVale\Compras\Dominio\StatusPedido;
use InvalidArgumentException;

final readonly class RejeitarPedidoCompra
{
    public function __construct(
        private PortaPedidoCompra $pedidos,
        private PortaRegistrarAuditoria $auditoria,
    ) {}

    /**
     * @return array{ok: bool, mensagem?: string}
     */
    public function executar(
        string $fazendaId,
        string $pedidoId,
        string $aprovadorId,
        string $motivo,
    ): array {
        try {
            $motivo = trim($motivo);
            if ($motivo === '') {
                return ['ok' => false, 'mensagem' => 'Motivo de rejeição obrigatório.'];
            }
            $pedido = $this->pedidos->obter($fazendaId, $pedidoId);
            if ($pedido === null) {
                return ['ok' => false, 'mensagem' => 'Pedido não encontrado.'];
            }
            if ($pedido['status'] !== StatusPedido::Solicitado->value) {
                return ['ok' => false, 'mensagem' => 'Só pedido solicitado pode ser rejeitado.'];
            }

            PoliticaAprovacao::garantirSegregacao($pedido['solicitante_id'], $aprovadorId);

            $this->pedidos->atualizarStatus(
                $fazendaId,
                $pedidoId,
                StatusPedido::Rejeitado,
                $aprovadorId,
                $motivo,
            );
            $this->auditoria->registrar(
                $fazendaId,
                $aprovadorId,
                'COMPRAS_PEDIDO_REJEITADO',
                'pedido_compra',
                $pedidoId,
                ['status' => StatusPedido::Solicitado->value],
                ['status' => StatusPedido::Rejeitado->value],
                $motivo,
                null,
                null,
            );

            return ['ok' => true];
        } catch (InvalidArgumentException $e) {
            return ['ok' => false, 'mensagem' => $e->getMessage()];
        }
    }
}
