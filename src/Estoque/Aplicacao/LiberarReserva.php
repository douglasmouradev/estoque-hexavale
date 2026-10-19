<?php

declare(strict_types=1);

namespace HexaVale\Estoque\Aplicacao;

use HexaVale\Auditoria\Aplicacao\PortaRegistrarAuditoria;
use HexaVale\Compartilhado\Dominio\Quantidade;
use HexaVale\Estoque\Aplicacao\Portas\PortaLedgerEstoque;
use HexaVale\Estoque\Dominio\TipoMovimentacao;
use InvalidArgumentException;

final readonly class LiberarReserva
{
    public function __construct(
        private PortaLedgerEstoque $ledger,
        private PortaRegistrarAuditoria $auditoria,
    ) {}

    /**
     * @return array{ok: bool, movimentacao_id?: string, mensagem?: string}
     */
    public function executar(
        string $fazendaId,
        string $loteId,
        string $quantidade,
        string $unidade,
        ?string $referenciaId,
        ?string $atorId,
        ?string $idempotencyKey,
    ): array {
        try {
            $q = Quantidade::de($quantidade);
            if (! $q->maiorQue(Quantidade::zero())) {
                return ['ok' => false, 'mensagem' => 'Quantidade de liberação deve ser positiva.'];
            }
            $id = $this->ledger->registrar(
                $fazendaId,
                $loteId,
                TipoMovimentacao::BaixaReserva,
                $q,
                $unidade,
                'Liberação de reserva',
                $atorId,
                $idempotencyKey,
                'ordem',
                $referenciaId,
            );
            $this->auditoria->registrar(
                $fazendaId,
                $atorId,
                'ESTOQUE_LIBERA_RESERVA',
                'lote',
                $loteId,
                null,
                ['quantidade' => $q->paraString()],
                null,
                null,
                null,
            );

            return ['ok' => true, 'movimentacao_id' => $id];
        } catch (InvalidArgumentException $e) {
            return ['ok' => false, 'mensagem' => $e->getMessage()];
        }
    }
}
