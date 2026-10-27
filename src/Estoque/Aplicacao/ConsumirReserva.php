<?php

declare(strict_types=1);

namespace HexaVale\Estoque\Aplicacao;

/**
 * Consome reserva de ordem: baixa_reserva + saída (físico).
 * Ordem típica após liberação (RN-01 / RN-08).
 */
final readonly class ConsumirReserva
{
    public function __construct(
        private LiberarReserva $liberar,
        private RegistrarSaida $saida,
    ) {}

    /**
     * @return array{ok: bool, mensagem?: string}
     */
    public function executar(
        string $fazendaId,
        string $loteId,
        string $quantidade,
        string $unidade,
        string $unidadeBase,
        ?string $validadeYmd,
        string $ordemId,
        ?string $atorId,
        string $idempotencyPrefix,
    ): array {
        $lib = $this->liberar->executar(
            $fazendaId,
            $loteId,
            $quantidade,
            $unidade,
            $ordemId,
            $atorId,
            $idempotencyPrefix.'-lib',
        );
        if (($lib['ok'] ?? false) !== true) {
            return ['ok' => false, 'mensagem' => $lib['mensagem'] ?? 'Falha ao baixar reserva.'];
        }

        $sai = $this->saida->executar(
            $fazendaId,
            $loteId,
            $quantidade,
            $unidade,
            $unidadeBase,
            $validadeYmd,
            'Baixa ordem '.$ordemId,
            $atorId,
            $idempotencyPrefix.'-sai',
        );
        if (($sai['ok'] ?? false) !== true) {
            return ['ok' => false, 'mensagem' => $sai['mensagem'] ?? 'Falha na saída da ordem.'];
        }

        return ['ok' => true];
    }
}
