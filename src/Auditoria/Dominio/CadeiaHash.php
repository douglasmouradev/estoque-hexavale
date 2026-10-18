<?php

declare(strict_types=1);

namespace HexaVale\Auditoria\Dominio;

final class CadeiaHash
{
    public function calcular(
        string $hashAnterior,
        string $ocorreuEm,
        string $atorId,
        string $acao,
        string $entidade,
        string $entidadeId,
        string $payloadCanonico,
    ): string {
        $material = implode('|', [
            $hashAnterior,
            $ocorreuEm,
            $atorId,
            $acao,
            $entidade,
            $entidadeId,
            $payloadCanonico,
        ]);

        return hash('sha256', $material);
    }

    public function genesis(): string
    {
        return str_repeat('0', 64);
    }
}
