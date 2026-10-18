<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use HexaVale\Auditoria\Aplicacao\PortaRegistrarAuditoria;
use HexaVale\Identidade\Aplicacao\Portas\PortaAutenticacao;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class SairController extends Controller
{
    public function __invoke(Request $request, PortaAutenticacao $auth, PortaRegistrarAuditoria $auditoria): RedirectResponse
    {
        $userId = $request->user()?->id;
        $fazenda = $request->session()->get('fazenda_id');
        $fazendaId = is_string($fazenda) ? $fazenda : null;

        $auditoria->registrar(
            $fazendaId,
            $userId !== null ? (string) $userId : null,
            'LOGOUT',
            'usuario',
            $userId !== null ? (string) $userId : '0',
            null,
            null,
            null,
            $request->ip(),
            $request->userAgent(),
        );
        $auth->encerrarSessao();

        return redirect()->route('entrar');
    }
}
