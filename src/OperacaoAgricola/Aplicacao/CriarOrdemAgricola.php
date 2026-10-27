<?php

declare(strict_types=1);

namespace HexaVale\OperacaoAgricola\Aplicacao;

use HexaVale\Auditoria\Aplicacao\PortaRegistrarAuditoria;
use HexaVale\Compartilhado\Dominio\Quantidade;
use HexaVale\OperacaoAgricola\Aplicacao\Portas\PortaOrdemAgricola;
use HexaVale\OperacaoAgricola\Dominio\FormulaDose;
use HexaVale\OperacaoAgricola\Dominio\TipoOrdem;
use InvalidArgumentException;

final readonly class CriarOrdemAgricola
{
    public function __construct(
        private PortaOrdemAgricola $ordens,
        private PortaRegistrarAuditoria $auditoria,
    ) {}

    /**
     * @param  list<array{lote_id: string, produto_id: string, dose: string, unidade: string}>  $itensDose
     * @return array{ok: bool, ordem_id?: string, mensagem?: string}
     */
    public function executar(
        string $fazendaId,
        string $talhaoId,
        string $tipoBruto,
        string $areaPlanejadaHa,
        ?string $caldaLHa,
        ?string $observacao,
        string $criadorId,
        array $itensDose,
    ): array {
        try {
            $tipo = TipoOrdem::from($tipoBruto);
            if ($itensDose === []) {
                return ['ok' => false, 'mensagem' => 'Ordem precisa de ao menos um item.'];
            }
            // RN-01: salvar rascunho não movimenta estoque
            Quantidade::de($areaPlanejadaHa); // valida
            $itens = [];
            foreach ($itensDose as $row) {
                $q = FormulaDose::calcular($tipo, $row['dose'], $areaPlanejadaHa, $caldaLHa);
                $itens[] = [
                    'lote_id' => $row['lote_id'],
                    'produto_id' => $row['produto_id'],
                    'dose' => $row['dose'],
                    'quantidade' => $q->paraString(),
                    'unidade' => $row['unidade'],
                ];
            }
            $id = $this->ordens->criar(
                $fazendaId,
                $talhaoId,
                $tipo,
                $areaPlanejadaHa,
                $caldaLHa,
                $observacao,
                $criadorId,
                $itens,
            );
            $this->auditoria->registrar(
                $fazendaId,
                $criadorId,
                'ORDEM_CRIADA',
                'ordem_agricola',
                $id,
                null,
                ['tipo' => $tipo->value, 'itens' => count($itens)],
                $observacao,
                null,
                null,
            );

            return ['ok' => true, 'ordem_id' => $id];
        } catch (InvalidArgumentException|\ValueError $e) {
            return ['ok' => false, 'mensagem' => $e->getMessage()];
        }
    }
}
