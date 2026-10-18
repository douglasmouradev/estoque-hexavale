<?php

declare(strict_types=1);

namespace HexaVale\Identidade\Infraestrutura;

use HexaVale\Identidade\Aplicacao\Portas\PortaTotp;
use InvalidArgumentException;
use OTPHP\TOTP;

final class OtphpTotp implements PortaTotp
{
    public function gerarSegredo(): string
    {
        return TOTP::generate()->getSecret();
    }

    public function uriProvisoria(string $segredo, string $rotulo): string
    {
        $this->exigirNaoVazio($segredo, 'segredo');
        $this->exigirNaoVazio($rotulo, 'rótulo');
        $totp = TOTP::createFromSecret($segredo);
        $totp->setLabel($rotulo);
        $totp->setIssuer('HexaVale Estoque');

        return $totp->getProvisioningUri();
    }

    public function verificar(string $segredo, string $codigo): bool
    {
        $this->exigirNaoVazio($segredo, 'segredo');
        $this->exigirNaoVazio($codigo, 'código');
        $totp = TOTP::createFromSecret($segredo);

        return $totp->verify($codigo, null, 1);
    }

    /**
     * @phpstan-assert non-empty-string $valor
     */
    private function exigirNaoVazio(string $valor, string $nome): void
    {
        if ($valor === '') {
            throw new InvalidArgumentException("{$nome} TOTP não pode ser vazio.");
        }
    }
}
