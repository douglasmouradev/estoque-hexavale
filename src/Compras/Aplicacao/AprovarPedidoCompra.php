<?php

declare(strict_types=1);

namespace HexaVale\Compras\Aplicacao;

use HexaVale\Auditoria\Aplicacao\PortaRegistrarAuditoria;
use HexaVale\Compras\Aplicacao\Portas\PortaPedidoCompra;
use HexaVale\Compras\Dominio\PoliticaAprovacao;
use HexaVale\Compras\Dominio\StatusPedido;
use InvalidArgumentException;

final readonly class AprovarPedidoCompra
{
    public function __construct(
        private PortaPedidoCompra $pedidos,
        private PortaRegistrarAuditoria $auditoria,
    ) {}

    /**
     * @return array{ok: bool, mensagem?: string}
     */
    public function executar(string $fazendaId, string $pedidoId, string $aprovadorId): array
    {
        try {
            $pedido = $this->pedidos->obter($fazendaId, $pedidoId);
            if ($pedido === null) {
                return ['ok' => false, 'mensagem' => 'Pedido não encontrado.'];
            }
            if ($pedido['status'] !== StatusPedido::Solicitado->value) {
                return ['ok' => false, 'mensagem' => 'Só pedido solicitado pode ser aprovado.'];
            }

            PoliticaAprovacao::garantirSegregacao($pedido['solicitante_id'], $aprovadorId);

            $this->pedidos->atualizarStatus(
                $fazendaId,
                $pedidoId,
                StatusPedido::Aprovado,
                $aprovadorId,
            );
            $this->auditoria->registrar(
                $fazendaId,
                $aprovadorId,
                'COMPRAS_PEDIDO_APROVADO',
                'pedido_compra',
                $pedidoId,
                ['status' => StatusPedido::Solicitado->value],
                ['status' => StatusPedido::Aprovado->value],
                null,
                null,
                null,
            );

            return ['ok' => true];
        } catch (InvalidArgumentException $e) {
            return ['ok' => false, 'mensagem' => $e->getMessage()];
        }
    }
}
