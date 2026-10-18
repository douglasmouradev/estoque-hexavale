<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use HexaVale\Compartilhado\Dominio\VersaoDominio;
use Illuminate\Http\JsonResponse;

final class HealthController extends Controller
{
    public function healthz(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'dominio' => VersaoDominio::rotulo(),
        ]);
    }

    public function readyz(): JsonResponse
    {
        return response()->json([
            'status' => 'ready',
        ]);
    }
}
