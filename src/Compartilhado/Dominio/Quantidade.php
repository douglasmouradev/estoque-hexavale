<?php

declare(strict_types=1);

namespace HexaVale\Compartilhado\Dominio;

use Brick\Math\BigDecimal;
use Brick\Math\Exception\MathException;
use Brick\Math\RoundingMode;
use InvalidArgumentException;

/** Quantidade decimal imutável — proibido float. */
final readonly class Quantidade
{
    private const ESCALA = 6;

    private function __construct(private BigDecimal $valor) {}

    public static function de(string $valor): self
    {
        try {
            $bd = BigDecimal::of($valor)->toScale(self::ESCALA, RoundingMode::HalfUp);
        } catch (MathException $e) {
            throw new InvalidArgumentException('Quantidade inválida: '.$valor, 0, $e);
        }

        return new self($bd);
    }

    public static function zero(): self
    {
        return self::de('0');
    }

    public function somar(self $outra): self
    {
        return new self($this->valor->plus($outra->valor)->toScale(self::ESCALA, RoundingMode::HalfUp));
    }

    public function subtrair(self $outra): self
    {
        return new self($this->valor->minus($outra->valor)->toScale(self::ESCALA, RoundingMode::HalfUp));
    }

    public function maiorQue(self $outra): bool
    {
        return $this->valor->isGreaterThan($outra->valor);
    }

    public function maiorOuIgual(self $outra): bool
    {
        return $this->valor->isGreaterThanOrEqualTo($outra->valor);
    }

    public function menorQue(self $outra): bool
    {
        return $this->valor->isLessThan($outra->valor);
    }

    public function isNegativa(): bool
    {
        return $this->valor->isNegative();
    }

    public function isZero(): bool
    {
        return $this->valor->isZero();
    }

    public function paraString(): string
    {
        return $this->valor->strippedOfTrailingZeros()->__toString();
    }

    public function __toString(): string
    {
        return $this->paraString();
    }
}
