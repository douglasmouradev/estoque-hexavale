<?php

declare(strict_types=1);

namespace HexaVale\Compras\Dominio;

use InvalidArgumentException;

/** MVP Q-04 — chave de acesso NF-e (44 dígitos); XML fica para fase futura. */
final readonly class ChaveNfe
{
    private function __construct(public string $valor) {}

    public static function de(string $bruta): self
    {
        $digits = preg_replace('/\D+/', '', $bruta) ?? '';
        if (strlen($digits) !== 44) {
            throw new InvalidArgumentException('Chave NF-e deve ter 44 dígitos (Q-04).');
        }

        return new self($digits);
    }
}
