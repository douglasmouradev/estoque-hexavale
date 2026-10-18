<?php

declare(strict_types=1);

namespace HexaVale\Identidade\Infraestrutura;

use HexaVale\Identidade\Aplicacao\Portas\PortaAutenticacao;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

final class LaravelAutenticacao implements PortaAutenticacao
{
    public function senhaConfere(string $hash, string $senha): bool
    {
        return Hash::check($senha, $hash);
    }

    public function iniciarSessao(string $userId): void
    {
        Auth::loginUsingId((int) $userId);
        Session::regenerate();
    }

    public function encerrarSessao(): void
    {
        Auth::logout();
        Session::invalidate();
        Session::regenerateToken();
    }

    public function guardarDesafioMfa(string $userId): void
    {
        Session::put('mfa_desafio_user_id', $userId);
    }

    public function desafioMfaAtual(): ?string
    {
        $v = Session::get('mfa_desafio_user_id');

        return is_string($v) ? $v : null;
    }

    public function limparDesafioMfa(): void
    {
        Session::forget('mfa_desafio_user_id');
    }
}
