<?php

declare(strict_types=1);

namespace HexaVale\Identidade\Aplicacao\Portas;

/**
 * Porta WebAuthn — implementação completa de cerimônia em BK-PASSKEY-01.
 * Dependência web-auth/webauthn-lib já instalada (ADR-0007).
 */
interface PortaPasskey
{
    /**
     * @return array<string, mixed>
     */
    public function opcoesRegistro(string $userId, string $userName, string $displayName): array;

    /**
     * @param  array<string, mixed>  $credencialCliente
     */
    public function concluirRegistro(string $userId, array $credencialCliente): void;
}
