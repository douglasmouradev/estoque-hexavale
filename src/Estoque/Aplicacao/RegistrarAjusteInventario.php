<?php

declare(strict_types=1);

namespace HexaVale\Estoque\Aplicacao;

use HexaVale\Auditoria\Aplicacao\PortaRegistrarAuditoria;
use HexaVale\Compartilhado\Dominio\ConversorUnidade;
use HexaVale\Compartilhado\Dominio\Quantidade;
use HexaVale\Estoque\Aplicacao\Portas\PortaLedgerEstoque;
use HexaVale\Estoque\Dominio\TipoMovimentacao;
use InvalidArgumentException;

/** RN-09 */
final readonly class RegistrarAjusteInventario
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
        string $contagem,
        string $unidadeInformada,
        string $unidadeBase,
        string $motivo,
        ?string $atorId,
        ?string $idempotencyKey,
    ): array {
        try {
            $motivo = trim($motivo);
            if ($motivo === '') {
                return ['ok' => false, 'mensagem' => 'Motivo do ajuste é obrigatório (RN-09).'];
            }

            $contagemQ = ConversorUnidade::converter(
                Quantidade::de($contagem),
                $unidadeInformada,
                $unidadeBase,
            );
            $atual = $this->ledger->saldoDoLote($fazendaId, $loteId);
            $novo = $atual->ajustarParaContagem($contagemQ);

            if ($novo->fisico->paraString() === $atual->fisico->paraString()) {
                return ['ok' => false, 'mensagem' => 'Sem diferença entre contagem e físico.'];
            }

            if ($novo->fisico->maiorQue($atual->fisico)) {
                $delta = $novo->fisico->subtrair($atual->fisico);
                $tipo = TipoMovimentacao::AjustePos;
            } else {
                $delta = $atual->fisico->subtrair($novo->fisico);
                $tipo = TipoMovimentacao::AjusteNeg;
            }

            $id = $this->ledger->registrar(
                $fazendaId,
                $loteId,
                $tipo,
                $delta,
                $unidadeBase,
                $motivo,
                $atorId,
                $idempotencyKey,
                'ajuste',
                null,
            );

            $this->auditoria->registrar(
                $fazendaId,
                $atorId,
                'ESTOQUE_AJUSTE',
                'lote',
                $loteId,
                ['fisico' => $atual->fisico->paraString()],
                ['fisico' => $novo->fisico->paraString(), 'motivo' => $motivo],
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
