<?php

declare(strict_types=1);

namespace Tests\Unit\Compartilhado;

use HexaVale\Compartilhado\Dominio\ConversorUnidade;
use HexaVale\Compartilhado\Dominio\Quantidade;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ConversorUnidadeTest extends TestCase
{
    public function test_converte_mesma_grandeza(): void
    {
        $q = ConversorUnidade::converter(Quantidade::de('1500'), 'g', 'kg');
        $this->assertSame('1.5', $q->paraString());
    }

    public function test_bloqueia_grandezas_diferentes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ConversorUnidade::converter(Quantidade::de('1'), 'kg', 'L');
    }
}
