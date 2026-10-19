<?php

declare(strict_types=1);

namespace Tests\Unit\Estoque;

use DateTimeImmutable;
use HexaVale\Estoque\Dominio\PoliticaLote;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PoliticaLoteTest extends TestCase
{
    public function test_lote_vencido_bloqueia(): void
    {
        $this->expectException(InvalidArgumentException::class);
        PoliticaLote::garantirUsoPermitido('2020-01-01', new DateTimeImmutable('2026-10-19'));
    }

    public function test_lote_valido_permite(): void
    {
        PoliticaLote::garantirUsoPermitido('2027-03-15', new DateTimeImmutable('2026-10-19'));
        $this->assertFalse(PoliticaLote::estaVencido('2027-03-15', new DateTimeImmutable('2026-10-19')));
    }
}
