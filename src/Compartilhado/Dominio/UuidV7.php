<?php

declare(strict_types=1);

namespace HexaVale\Compartilhado\Dominio;

use Symfony\Component\Uid\Uuid;

final readonly class UuidV7
{
    private function __construct(public string $valor) {}

    public static function gerar(): self
    {
        return new self(Uuid::v7()->toRfc4122());
    }

    public static function de(string $valor): self
    {
        if (! Uuid::isValid($valor)) {
            throw new \InvalidArgumentException('UUID inválido.');
        }

        return new self($valor);
    }

    public function __toString(): string
    {
        return $this->valor;
    }
}
