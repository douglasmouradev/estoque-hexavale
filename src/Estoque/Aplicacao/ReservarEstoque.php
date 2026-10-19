<?php

declare(strict_types=1);

namespace HexaVale\Estoque\Aplicacao;

use HexaVale\Auditoria\Aplicacao\PortaRegistrarAuditoria;
use HexaVale\Compartilhado\Dominio\Quantidade;
use HexaVale\Estoque\Aplicacao\Portas\PortaLedgerEstoque;
use HexaVale\Estoque\Dominio\PoliticaLote;
use HexaVale\Estoque\Dominio\TipoMovimentacao;
use InvalidArgumentException;

final readonly class ReservarEstoque
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
        ?string $validadeYmd,
        ?string $referenciaId,
        ?string $atorId,
        ?string $idempotencyKey,
    ): array {
        try {
            PoliticaLote::garantirUsoPermitido($validadeYmd);
            $q = Quantidade::de($quantidade);
            if (! $q->maiorQue(Quantidade::zero())) {
                return ['ok' => false, 'mensagem' => 'Quantidade de reserva deve ser positiva.'];
            }
            $id = $this->ledger->registrar(
                $fazendaId,
                $loteId,
                TipoMovimentacao::Reserva,
                $q,
                $unidade,
                'Reserva de estoque',
                $atorId,
                $idempotencyKey,
                'ordem',
                $referenciaId,
            );
            $this->auditoria->registrar(
                $fazendaId,
                $atorId,
                'ESTOQUE_RESERVA',
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
