<?php

declare(strict_types=1);

namespace Tests\Unit\Auditoria;

use HexaVale\Auditoria\Dominio\CadeiaHash;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CadeiaHashTest extends TestCase
{
    #[Test]
    public function hash_encadeado_muda_com_payload(): void
    {
        $c = new CadeiaHash;
        $a = $c->calcular($c->genesis(), 't', '1', 'LOGIN', 'usuario', '1', '{}');
        $b = $c->calcular($a, 't2', '1', 'LOGIN', 'usuario', '1', '{"x":1}');
        self::assertNotSame($a, $b);
        self::assertSame(64, strlen($a));
    }
}
