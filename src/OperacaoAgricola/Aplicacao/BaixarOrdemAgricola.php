<?php

declare(strict_types=1);

namespace HexaVale\OperacaoAgricola\Aplicacao;

use HexaVale\Auditoria\Aplicacao\PortaRegistrarAuditoria;
use HexaVale\Compartilhado\Dominio\Quantidade;
use HexaVale\Estoque\Aplicacao\ConsumirReserva;
use HexaVale\Estoque\Aplicacao\LiberarReserva;
use HexaVale\OperacaoAgricola\Aplicacao\Portas\PortaOrdemAgricola;
use HexaVale\OperacaoAgricola\Dominio\FormulaDose;
use HexaVale\OperacaoAgricola\Dominio\PoliticaAreaTratada;
use HexaVale\OperacaoAgricola\Dominio\StatusOrdem;
use HexaVale\OperacaoAgricola\Dominio\TipoOrdem;
use InvalidArgumentException;

final readonly class BaixarOrdemAgricola
{
    public function __construct(
        private PortaOrdemAgricola $ordens,
        private ConsumirReserva $consumir,
        private LiberarReserva $liberarSobra,
        private PortaRegistrarAuditoria $auditoria,
    ) {}

    /**
     * RN-08 — baixa uma vez; RN-06 — área tratada; consome reserva proporcional.
     *
     * @return array{ok: bool, mensagem?: string}
     */
    public function executar(
        string $fazendaId,
        string $ordemId,
        string $areaTratadaHa,
        string $atorId,
        string $idempotencyPrefix,
    ): array {
        try {
            $ordem = $this->ordens->obter($fazendaId, $ordemId);
            if ($ordem === null) {
                return ['ok' => false, 'mensagem' => 'Ordem não encontrada.'];
            }
            if ($ordem['status'] !== StatusOrdem::Liberada->value) {
                return ['ok' => false, 'mensagem' => 'Só ordem liberada pode ser baixada (RN-08).'];
            }

            PoliticaAreaTratada::garantir($areaTratadaHa, $ordem['area_planejada_ha']);
            $tipo = TipoOrdem::from($ordem['tipo']);

            foreach ($ordem['itens'] as $item) {
                $qConsumo = FormulaDose::calcular(
                    $tipo,
                    $item['dose'],
                    $areaTratadaHa,
                    $ordem['calda_l_ha'],
                );
                $r = $this->consumir->executar(
                    $fazendaId,
                    $item['lote_id'],
                    $qConsumo->paraString(),
                    $item['unidade'],
                    $item['unidade'],
                    $item['validade'],
                    $ordemId,
                    $atorId,
                    $idempotencyPrefix.'-'.$item['id'],
                );
                if (($r['ok'] ?? false) !== true) {
                    return ['ok' => false, 'mensagem' => $r['mensagem'] ?? 'Falha ao consumir reserva.'];
                }

                $qReservada = Quantidade::de($item['quantidade']);
                $sobra = $qReservada->subtrair($qConsumo);
                if ($sobra->maiorQue(Quantidade::zero())) {
                    $lib = $this->liberarSobra->executar(
                        $fazendaId,
                        $item['lote_id'],
                        $sobra->paraString(),
                        $item['unidade'],
                        $ordemId,
                        $atorId,
                        $idempotencyPrefix.'-'.$item['id'].'-sobra',
                    );
                    if (($lib['ok'] ?? false) !== true) {
                        return ['ok' => false, 'mensagem' => $lib['mensagem'] ?? 'Falha ao liberar sobra.'];
                    }
                }
            }

            $this->ordens->atualizarStatus($fazendaId, $ordemId, StatusOrdem::Baixada, $areaTratadaHa);
            $this->auditoria->registrar(
                $fazendaId,
                $atorId,
                'ORDEM_BAIXADA',
                'ordem_agricola',
                $ordemId,
                ['status' => StatusOrdem::Liberada->value],
                ['status' => StatusOrdem::Baixada->value, 'area_tratada_ha' => $areaTratadaHa],
                null,
                null,
                null,
            );

            return ['ok' => true];
        } catch (InvalidArgumentException|\ValueError $e) {
            return ['ok' => false, 'mensagem' => $e->getMessage()];
        }
    }
}
