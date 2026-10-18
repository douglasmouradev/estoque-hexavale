<?php

declare(strict_types=1);

namespace HexaVale\Cadastros\Aplicacao;

use HexaVale\Auditoria\Aplicacao\PortaRegistrarAuditoria;
use HexaVale\Cadastros\Aplicacao\Portas\PortaProdutoEscrita;

/** RN-13 — cadastro referenciado não exclui. */
final readonly class ExcluirProduto
{
    public function __construct(
        private PortaProdutoEscrita $produtos,
        private PortaRegistrarAuditoria $auditoria,
    ) {}

    /**
     * @return array{ok: bool, mensagem?: string}
     */
    public function executar(string $fazendaId, string $produtoId, ?string $atorId): array
    {
        $resultado = $this->produtos->excluirSeNaoReferenciado($fazendaId, $produtoId);
        if (($resultado['ok'] ?? false) !== true) {
            return $resultado;
        }

        $this->auditoria->registrar(
            $fazendaId,
            $atorId,
            'PRODUTO_EXCLUIDO',
            'produto',
            $produtoId,
            null,
            null,
            null,
            null,
            null,
        );

        return ['ok' => true];
    }
}
