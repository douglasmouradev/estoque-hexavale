<?php

declare(strict_types=1);

namespace Tests\Unit\Privacidade;

use HexaVale\Privacidade\Infraestrutura\SodiumCifradorCampo;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SodiumCifradorCampoTest extends TestCase
{
    #[Test]
    public function round_trip_e_indice_cego_estavel(): void
    {
        $k1 = base64_encode(random_bytes(32));
        $k2 = base64_encode(random_bytes(32));
        $c = new SodiumCifradorCampo($k1, $k2);
        $cipher = $c->cifrar('52998224725');
        self::assertNotSame('52998224725', $cipher);
        self::assertSame('52998224725', $c->decifrar($cipher));
        self::assertSame($c->indiceCego('52998224725'), $c->indiceCego('52998224725'));
    }
}
