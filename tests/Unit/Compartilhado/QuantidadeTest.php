<?php

declare(strict_types=1);

namespace Tests\Unit\Compartilhado;

use HexaVale\Compartilhado\Dominio\Quantidade;
use PHPUnit\Framework\TestCase;

final class QuantidadeTest extends TestCase
{
    public function test_soma_e_subtrai_sem_float(): void
    {
        $a = Quantidade::de('18.5');
        $b = Quantidade::de('1.25');
        $this->assertSame('19.75', $a->somar($b)->paraString());
        $this->assertSame('17.25', $a->subtrair($b)->paraString());
    }
}
