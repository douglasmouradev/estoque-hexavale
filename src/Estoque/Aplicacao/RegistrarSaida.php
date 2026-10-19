<?php

declare(strict_types=1);

namespace HexaVale\Estoque\Aplicacao;

use HexaVale\Auditoria\Aplicacao\PortaRegistrarAuditoria;
use HexaVale\Compartilhado\Dominio\ConversorUnidade;
use HexaVale\Compartilhado\Dominio\Quantidade;
use HexaVale\Estoque\Aplicacao\Portas\PortaLedgerEstoque;
use HexaVale\Estoque\Dominio\PoliticaLote;
use HexaVale\Estoque\Dominio\TipoMovimentacao;
use InvalidArgumentException;

final readonly class RegistrarSaida
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
        ?string $validadeYmd,
        ?string $motivo,
        ?string $atorId,
        ?string $idempotencyKey,
    ): array {
        try {
            PoliticaLote::garantirUsoPermitido($validadeYmd);
            $q = ConversorUnidade::converter(
                Quantidade::de($quantidade),
                $unidadeInformada,
                $unidadeBase,
            );
            if (! $q->maiorQue(Quantidade::zero())) {
                return ['ok' => false, 'mensagem' => 'Quantidade de saída deve ser positiva.'];
            }
            $id = $this->ledger->registrar(
                $fazendaId,
                $loteId,
                TipoMovimentacao::Saida,
                $q,
                $unidadeBase,
                $motivo,
                $atorId,
                $idempotencyKey,
            );
            $this->auditoria->registrar(
                $fazendaId,
                $atorId,
                'ESTOQUE_SAIDA',
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
