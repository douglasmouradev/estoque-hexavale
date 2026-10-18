<?php

declare(strict_types=1);

namespace Tests\Unit\Compartilhado;

use HexaVale\Compartilhado\Dominio\VersaoDominio;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class VersaoDominioTest extends TestCase
{
    #[Test]
    public function rotulo_inclui_fase_zero(): void
    {
        self::assertSame('0', VersaoDominio::FASE);
        self::assertStringContainsString('Fase 0', VersaoDominio::rotulo());
    }
}
