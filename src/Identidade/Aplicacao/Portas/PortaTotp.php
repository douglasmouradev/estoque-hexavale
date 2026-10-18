<?php

declare(strict_types=1);

namespace HexaVale\Identidade\Aplicacao\Portas;

interface PortaTotp
{
    public function gerarSegredo(): string;

    public function uriProvisoria(string $segredo, string $rotulo): string;

    public function verificar(string $segredo, string $codigo): bool;
}
