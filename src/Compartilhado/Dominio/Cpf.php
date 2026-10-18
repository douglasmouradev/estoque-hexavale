<?php

declare(strict_types=1);

namespace HexaVale\Compartilhado\Dominio;

final readonly class Cpf
{
    private function __construct(public string $digitos) {}

    public static function de(string $valor): self
    {
        $digitos = preg_replace('/\D+/', '', $valor) ?? '';
        if (strlen($digitos) !== 11 || preg_match('/^(\d)\1{10}$/', $digitos) === 1) {
            throw new \InvalidArgumentException('CPF inválido.');
        }

        for ($t = 9; $t < 11; $t++) {
            $soma = 0;
            for ($i = 0; $i < $t; $i++) {
                $soma += (int) $digitos[$i] * (($t + 1) - $i);
            }
            $digito = ((10 * $soma) % 11) % 10;
            if ((int) $digitos[$t] !== $digito) {
                throw new \InvalidArgumentException('CPF inválido.');
            }
        }

        return new self($digitos);
    }

    public function formatado(): string
    {
        return substr($this->digitos, 0, 3).'.'
            .substr($this->digitos, 3, 3).'.'
            .substr($this->digitos, 6, 3).'-'
            .substr($this->digitos, 9, 2);
    }

    public function __toString(): string
    {
        return $this->digitos;
    }
}
