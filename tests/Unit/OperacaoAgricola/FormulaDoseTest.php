<?php

declare(strict_types=1);

namespace Tests\Unit\OperacaoAgricola;

use HexaVale\OperacaoAgricola\Dominio\FormulaDose;
use HexaVale\OperacaoAgricola\Dominio\PoliticaAreaTratada;
use HexaVale\OperacaoAgricola\Dominio\TipoOrdem;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class FormulaDoseTest extends TestCase
{
    public function test_adubacao_dose_vezes_area(): void
    {
        $q = FormulaDose::calcular(TipoOrdem::Adubacao, '10', '2.5');
        $this->assertSame('25', $q->paraString());
    }

    public function test_pulverizacao_com_calda_por_100l(): void
    {
        // 200 g / 100 L × 100 L/ha × 2 ha = 400 g
        $q = FormulaDose::calcular(TipoOrdem::Pulverizacao, '200', '2', '100');
        $this->assertSame('400', $q->paraString());
    }

    public function test_rn06_area_tratada_invalida(): void
    {
        $this->expectException(InvalidArgumentException::class);
        PoliticaAreaTratada::garantir('3', '2');
    }

    public function test_rn06_area_tratada_ok(): void
    {
        $q = PoliticaAreaTratada::garantir('1.5', '2');
        $this->assertSame('1.5', $q->paraString());
    }
}
