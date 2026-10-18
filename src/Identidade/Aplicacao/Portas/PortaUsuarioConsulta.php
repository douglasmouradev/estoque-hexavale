<?php

declare(strict_types=1);

namespace HexaVale\Identidade\Aplicacao\Portas;

use HexaVale\Compartilhado\Dominio\Email;

interface PortaUsuarioConsulta
{
    /**
     * @return array{id: string, uuid: string, name: string, email: string, password: string, mfa_enabled: bool, mfa_secret: string|null}|null
     */
    public function buscarPorEmail(Email $email): ?array;

    /**
     * @return array{id: string, uuid: string, name: string, email: string, password: string, mfa_enabled: bool, mfa_secret: string|null}|null
     */
    public function buscarPorId(string $id): ?array;
}
