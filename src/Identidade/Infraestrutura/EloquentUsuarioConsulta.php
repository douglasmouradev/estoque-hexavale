<?php

declare(strict_types=1);

namespace HexaVale\Identidade\Infraestrutura;

use App\Models\User;
use HexaVale\Compartilhado\Dominio\Email;
use HexaVale\Identidade\Aplicacao\Portas\PortaUsuarioConsulta;
use HexaVale\Privacidade\Dominio\CifradorCampo;

final readonly class EloquentUsuarioConsulta implements PortaUsuarioConsulta
{
    public function __construct(private CifradorCampo $cifrador) {}

    public function buscarPorEmail(Email $email): ?array
    {
        $user = User::query()->where('email', $email->valor)->first();

        return $user === null ? null : $this->mapear($user);
    }

    public function buscarPorId(string $id): ?array
    {
        $user = User::query()->find((int) $id);

        return $user === null ? null : $this->mapear($user);
    }

    /**
     * @return array{id: string, uuid: string, name: string, email: string, password: string, mfa_enabled: bool, mfa_secret: string|null}
     */
    private function mapear(User $user): array
    {
        $secret = null;
        $cifrado = $user->mfa_secret_cifrado;
        if (is_string($cifrado) && $cifrado !== '') {
            $secret = $this->cifrador->decifrar($cifrado);
        }

        return [
            'id' => (string) $user->id,
            'uuid' => (string) $user->uuid,
            'name' => (string) $user->name,
            'email' => (string) $user->email,
            'password' => (string) $user->password,
            'mfa_enabled' => (bool) $user->mfa_enabled,
            'mfa_secret' => $secret,
        ];
    }
}
