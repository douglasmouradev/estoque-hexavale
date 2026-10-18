<?php

declare(strict_types=1);

namespace Tests\Unit\Estoque;

use HexaVale\Compartilhado\Dominio\Quantidade;
use HexaVale\Estoque\Dominio\SaldoLote;
use HexaVale\Estoque\Dominio\SituacaoSaldo;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class SaldoLoteTest extends TestCase
{
    public function test_disponivel_eh_fisico_menos_reservado(): void
    {
        $s = new SaldoLote(Quantidade::de('100'), Quantidade::de('35'));
        $this->assertSame('65', $s->disponivel()->paraString());
    }

    public function test_saida_acima_do_disponivel_falha(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $s = new SaldoLote(Quantidade::de('10'), Quantidade::de('4'));
        $s->aplicarSaida(Quantidade::de('7'));
    }

    public function test_situacao_abaixo_do_minimo(): void
    {
        $s = new SaldoLote(Quantidade::de('3'), Quantidade::zero());
        $this->assertSame(SituacaoSaldo::Abaixo, $s->situacao(Quantidade::de('5')));
    }
}
