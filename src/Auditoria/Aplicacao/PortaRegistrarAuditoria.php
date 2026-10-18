<?php

declare(strict_types=1);

namespace HexaVale\Auditoria\Aplicacao;

interface PortaRegistrarAuditoria
{
    /**
     * @param  array<string, mixed>|null  $antes
     * @param  array<string, mixed>|null  $depois
     */
    public function registrar(
        ?string $fazendaId,
        ?string $atorId,
        string $acao,
        string $entidade,
        string $entidadeId,
        ?array $antes,
        ?array $depois,
        ?string $motivo,
        ?string $ip,
        ?string $userAgent,
    ): string;
}
