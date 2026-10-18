<?php

declare(strict_types=1);

namespace Tests\Unit\Privacidade;

use HexaVale\Privacidade\Dominio\MascaradorPii;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MascaradorPiiTest extends TestCase
{
    #[Test]
    public function mascara_cpf_email_telefone(): void
    {
        $m = new MascaradorPii;
        self::assertSame('***.982.247-**', $m->cpf('52998224725'));
        self::assertSame('a***@hexavale.local', $m->email('admin@hexavale.local'));
        self::assertSame('(**) *****-0000', $m->telefone('87999990000'));
    }
}
