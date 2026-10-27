<?php

declare(strict_types=1);

namespace Tests\Unit\Compras;

use HexaVale\Compras\Dominio\ChaveNfe;
use HexaVale\Compras\Dominio\PoliticaAprovacao;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ChaveNfeTest extends TestCase
{
    public function test_aceita_44_digitos(): void
    {
        $chave = ChaveNfe::de('35240112345678901234567890123456789012345678');
        $this->assertSame('35240112345678901234567890123456789012345678', $chave->valor);
    }

    public function test_rejeita_tamanho_invalido(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ChaveNfe::de('123');
    }

    public function test_rn12_bloqueia_autoaprovacao(): void
    {
        $this->expectException(InvalidArgumentException::class);
        PoliticaAprovacao::garantirSegregacao('user-1', 'user-1');
    }

    public function test_rn12_permite_aprovadores_diferentes(): void
    {
        $this->expectNotToPerformAssertions();
        PoliticaAprovacao::garantirSegregacao('user-1', 'user-2');
    }
}
