<?php

declare(strict_types=1);

namespace HexaVale\Identidade\Aplicacao\Portas;

interface PortaAutenticacao
{
    public function senhaConfere(string $hash, string $senha): bool;

    public function iniciarSessao(string $userId): void;

    public function encerrarSessao(): void;

    public function guardarDesafioMfa(string $userId): void;

    public function desafioMfaAtual(): ?string;

    public function limparDesafioMfa(): void;
}
