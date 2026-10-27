<?php

declare(strict_types=1);

namespace HexaVale\Compras\Aplicacao;

use HexaVale\Auditoria\Aplicacao\PortaRegistrarAuditoria;
use HexaVale\Compras\Aplicacao\Portas\PortaPedidoCompra;
use HexaVale\Compras\Dominio\StatusPedido;

final readonly class SolicitarAprovacaoPedido
{
    public function __construct(
        private PortaPedidoCompra $pedidos,
        private PortaRegistrarAuditoria $auditoria,
    ) {}

    /**
     * @return array{ok: bool, mensagem?: string}
     */
    public function executar(string $fazendaId, string $pedidoId, string $atorId): array
    {
        $pedido = $this->pedidos->obter($fazendaId, $pedidoId);
        if ($pedido === null) {
            return ['ok' => false, 'mensagem' => 'Pedido não encontrado.'];
        }
        if ($pedido['status'] !== StatusPedido::Rascunho->value) {
            return ['ok' => false, 'mensagem' => 'Só rascunho pode ser enviado para aprovação.'];
        }
        if ($pedido['itens'] === []) {
            return ['ok' => false, 'mensagem' => 'Pedido sem itens.'];
        }

        $this->pedidos->atualizarStatus($fazendaId, $pedidoId, StatusPedido::Solicitado);
        $this->auditoria->registrar(
            $fazendaId,
            $atorId,
            'COMPRAS_PEDIDO_SOLICITADO',
            'pedido_compra',
            $pedidoId,
            ['status' => StatusPedido::Rascunho->value],
            ['status' => StatusPedido::Solicitado->value],
            null,
            null,
            null,
        );

        return ['ok' => true];
    }
}
