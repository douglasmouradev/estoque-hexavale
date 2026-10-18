<?php

declare(strict_types=1);

namespace HexaVale\Compartilhado\Dominio;

final readonly class Email
{
    private function __construct(public string $valor) {}

    public static function de(string $valor): self
    {
        $normalizado = mb_strtolower(trim($valor));
        if (filter_var($normalizado, FILTER_VALIDATE_EMAIL) === false) {
            throw new \InvalidArgumentException('E-mail inválido.');
        }

        return new self($normalizado);
    }

    public function __toString(): string
    {
        return $this->valor;
    }
}
