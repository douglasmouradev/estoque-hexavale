<?php

declare(strict_types=1);

namespace HexaVale\Estoque\Aplicacao;

use HexaVale\Auditoria\Aplicacao\PortaRegistrarAuditoria;
use HexaVale\Compartilhado\Dominio\ConversorUnidade;
use HexaVale\Compartilhado\Dominio\Quantidade;
use HexaVale\Estoque\Aplicacao\Portas\PortaLedgerEstoque;
use HexaVale\Estoque\Dominio\TipoMovimentacao;
use InvalidArgumentException;

final readonly class RegistrarEntrada
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
        string $unidadeInformada,
        string $unidadeBase,
        ?string $motivo,
        ?string $atorId,
        ?string $idempotencyKey,
    ): array {
        try {
            $q = ConversorUnidade::converter(
                Quantidade::de($quantidade),
                $unidadeInformada,
                $unidadeBase,
            );
            if (! $q->maiorQue(Quantidade::zero())) {
                return ['ok' => false, 'mensagem' => 'Quantidade de entrada deve ser positiva.'];
            }
            $id = $this->ledger->registrar(
                $fazendaId,
                $loteId,
                TipoMovimentacao::Entrada,
                $q,
                $unidadeBase,
                $motivo,
                $atorId,
                $idempotencyKey,
            );
            $this->auditoria->registrar(
                $fazendaId,
                $atorId,
                'ESTOQUE_ENTRADA',
                'lote',
                $loteId,
                null,
                ['quantidade' => $q->paraString(), 'unidade' => $unidadeBase],
                $motivo,
                null,
                null,
            );

            return ['ok' => true, 'movimentacao_id' => $id];
        } catch (InvalidArgumentException $e) {
            return ['ok' => false, 'mensagem' => $e->getMessage()];
        }
    }
}
