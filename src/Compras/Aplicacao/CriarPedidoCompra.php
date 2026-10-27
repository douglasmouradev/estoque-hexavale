<?php

declare(strict_types=1);

namespace HexaVale\Compras\Aplicacao;

use HexaVale\Auditoria\Aplicacao\PortaRegistrarAuditoria;
use HexaVale\Compartilhado\Dominio\Quantidade;
use HexaVale\Compras\Aplicacao\Portas\PortaPedidoCompra;
use InvalidArgumentException;

final readonly class CriarPedidoCompra
{
    public function __construct(
        private PortaPedidoCompra $pedidos,
        private PortaRegistrarAuditoria $auditoria,
    ) {}

    /**
     * @param  list<array{produto_id: string, quantidade: string, unidade: string}>  $itens
     * @return array{ok: bool, pedido_id?: string, mensagem?: string}
     */
    public function executar(
        string $fazendaId,
        string $solicitanteId,
        ?string $fornecedorNome,
        ?string $observacao,
        array $itens,
    ): array {
        try {
            if ($itens === []) {
                return ['ok' => false, 'mensagem' => 'Pedido precisa de ao menos um item.'];
            }
            foreach ($itens as $item) {
                $q = Quantidade::de((string) ($item['quantidade'] ?? '0'));
                if (! $q->maiorQue(Quantidade::zero())) {
                    return ['ok' => false, 'mensagem' => 'Quantidade de item deve ser positiva.'];
                }
                if (trim((string) ($item['produto_id'] ?? '')) === '') {
                    return ['ok' => false, 'mensagem' => 'Produto do item é obrigatório.'];
                }
            }
            $id = $this->pedidos->criar(
                $fazendaId,
                $fornecedorNome,
                $observacao,
                $solicitanteId,
                $itens,
            );
            $this->auditoria->registrar(
                $fazendaId,
                $solicitanteId,
                'COMPRAS_PEDIDO_CRIADO',
                'pedido_compra',
                $id,
                null,
                ['itens' => count($itens)],
                $observacao,
                null,
                null,
            );

            return ['ok' => true, 'pedido_id' => $id];
        } catch (InvalidArgumentException $e) {
            return ['ok' => false, 'mensagem' => $e->getMessage()];
        }
    }
}
