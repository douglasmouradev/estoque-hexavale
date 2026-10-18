<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AuditoriaEvento;
use HexaVale\Auditoria\Aplicacao\PortaRegistrarAuditoria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AuditoriaImutavelTest extends TestCase
{
    use RefreshDatabase;

    public function test_nao_permite_update_nem_delete(): void
    {
        /** @var PortaRegistrarAuditoria $reg */
        $reg = app(PortaRegistrarAuditoria::class);
        $id = $reg->registrar(null, '1', 'TESTE', 'sistema', '0', null, null, null, null, null);

        $this->expectException(\Throwable::class);
        AuditoriaEvento::query()->whereKey($id)->update(['acao' => 'HACK']);
    }
}
