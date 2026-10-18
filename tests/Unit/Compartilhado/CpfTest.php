<?php

declare(strict_types=1);

namespace Tests\Unit\Compartilhado;

use HexaVale\Compartilhado\Dominio\Cpf;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CpfTest extends TestCase
{
    #[Test]
    public function aceita_cpf_valido(): void
    {
        $cpf = Cpf::de('529.982.247-25');
        self::assertSame('52998224725', $cpf->digitos);
    }

    #[Test]
    public function rejeita_cpf_invalido(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Cpf::de('11111111111');
    }
}
