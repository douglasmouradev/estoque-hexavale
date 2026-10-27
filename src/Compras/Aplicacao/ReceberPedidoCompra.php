<?php

declare(strict_types=1);

namespace HexaVale\Compras\Aplicacao;

use HexaVale\Auditoria\Aplicacao\PortaRegistrarAuditoria;
use HexaVale\Compras\Aplicacao\Portas\PortaPedidoCompra;
use HexaVale\Compras\Dominio\ChaveNfe;
use HexaVale\Compras\Dominio\StatusPedido;
use HexaVale\Estoque\Aplicacao\RegistrarEntrada;
use InvalidArgumentException;

final readonly class ReceberPedidoCompra
{
    public function __construct(
        private PortaPedidoCompra $pedidos,
        private RegistrarEntrada $entrada,
        private PortaRegistrarAuditoria $auditoria,
    ) {}

    /**
     * @param  list<array{item_id: string, lote_codigo: string, validade?: ?string}>  $lotesPorItem
     * @return array{ok: bool, mensagem?: string}
     */
    public function executar(
        string $fazendaId,
        string $pedidoId,
        string $chaveNfeBruta,
        array $lotesPorItem,
        string $atorId,
        string $idempotencyPrefix,
    ): array {
        try {
            $chave = ChaveNfe::de($chaveNfeBruta);
            $pedido = $this->pedidos->obter($fazendaId, $pedidoId);
            if ($pedido === null) {
                return ['ok' => false, 'mensagem' => 'Pedido não encontrado.'];
            }
            if ($pedido['status'] !== StatusPedido::Aprovado->value) {
                return ['ok' => false, 'mensagem' => 'Só pedido aprovado pode ser recebido.'];
            }
            if ($pedido['itens'] === []) {
                return ['ok' => false, 'mensagem' => 'Pedido sem itens.'];
            }

            $mapaLotes = [];
            foreach ($lotesPorItem as $row) {
                $itemId = (string) ($row['item_id'] ?? '');
                $codigo = trim((string) ($row['lote_codigo'] ?? ''));
                if ($itemId === '' || $codigo === '') {
                    return ['ok' => false, 'mensagem' => 'Informe lote para cada item no recebimento.'];
                }
                $mapaLotes[$itemId] = [
                    'codigo' => $codigo,
                    'validade' => isset($row['validade']) && $row['validade'] !== ''
                        ? (string) $row['validade']
                        : null,
                ];
            }

            foreach ($pedido['itens'] as $item) {
                $info = $mapaLotes[$item['id']] ?? null;
                if ($info === null) {
                    return ['ok' => false, 'mensagem' => 'Falta lote para item '.$item['produto'].'.'];
                }
                $loteId = $this->pedidos->garantirLote(
                    $fazendaId,
                    $item['produto_id'],
                    $info['codigo'],
                    $info['validade'],
                );
                $r = $this->entrada->executar(
                    $fazendaId,
                    $loteId,
                    $item['quantidade'],
                    $item['unidade'],
                    $item['unidade_base'],
                    'NF-e '.$chave->valor,
                    $atorId,
                    $idempotencyPrefix.'-'.$item['id'],
                );
                if (($r['ok'] ?? false) !== true) {
                    return ['ok' => false, 'mensagem' => $r['mensagem'] ?? 'Falha na entrada do item.'];
                }
            }

            // Re-vincula referência no ledger via motivo; atualiza pedido
            $this->pedidos->atualizarStatus(
                $fazendaId,
                $pedidoId,
                StatusPedido::Recebido,
                null,
                null,
                $chave->valor,
            );
            $this->auditoria->registrar(
                $fazendaId,
                $atorId,
                'COMPRAS_PEDIDO_RECEBIDO',
                'pedido_compra',
                $pedidoId,
                ['status' => StatusPedido::Aprovado->value],
                ['status' => StatusPedido::Recebido->value, 'chave_nfe' => $chave->valor],
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
