<?php

declare(strict_types=1);

namespace HexaVale\Auditoria\Dominio;

final readonly class EventoAuditoria
{
    /**
     * @param  array<string, mixed>|null  $antes
     * @param  array<string, mixed>|null  $depois
     */
    public function __construct(
        public string $id,
        public ?string $fazendaId,
        public ?string $atorId,
        public string $acao,
        public string $entidade,
        public string $entidadeId,
        public ?array $antes,
        public ?array $depois,
        public ?string $motivo,
        public ?string $ip,
        public ?string $userAgent,
        public string $ocorreuEm,
        public string $hashAnterior,
        public string $hash,
    ) {}
}
