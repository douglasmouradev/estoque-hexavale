<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use HexaVale\Auditoria\Aplicacao\PortaRegistrarAuditoria;
use HexaVale\Identidade\Aplicacao\Entrar;
use HexaVale\Identidade\Aplicacao\VerificarTotp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class EntrarController extends Controller
{
    public function formulario(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('painel');
        }

        return view('auth.entrar');
    }

    public function autenticar(Request $request, Entrar $entrar, PortaRegistrarAuditoria $auditoria): RedirectResponse|View
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = $request->string('email')->toString();
        $password = $request->string('password')->toString();

        $resultado = $entrar->executar($email, $password);
        if ($resultado['ok'] !== true) {
            return back()->withErrors(['email' => $resultado['mensagem'] ?? 'Falha'])->onlyInput('email');
        }

        if (($resultado['precisa_totp'] ?? false) === true) {
            return view('auth.totp', ['userId' => $resultado['user_id'] ?? '']);
        }

        $userId = $resultado['user_id'] ?? '';
        $fazendaId = $this->fazendaDaSessao($request);
        $auditoria->registrar(
            $fazendaId,
            $userId !== '' ? $userId : null,
            'LOGIN',
            'usuario',
            $userId,
            null,
            null,
            null,
            $request->ip(),
            $request->userAgent(),
        );

        return redirect()->intended(route('painel'));
    }

    public function verificarTotp(Request $request, VerificarTotp $verificar, PortaRegistrarAuditoria $auditoria): RedirectResponse
    {
        $request->validate([
            'user_id' => ['required', 'string'],
            'codigo' => ['required', 'string', 'size:6'],
        ]);

        $userId = $request->string('user_id')->toString();
        $codigo = $request->string('codigo')->toString();

        $resultado = $verificar->executar($userId, $codigo);
        if ($resultado['ok'] !== true) {
            return back()->withErrors(['codigo' => $resultado['mensagem'] ?? 'Código inválido']);
        }

        $auditoria->registrar(
            $this->fazendaDaSessao($request),
            $userId,
            'LOGIN_MFA',
            'usuario',
            $userId,
            null,
            null,
            null,
            $request->ip(),
            $request->userAgent(),
        );

        return redirect()->intended(route('painel'));
    }

    private function fazendaDaSessao(Request $request): ?string
    {
        $v = $request->session()->get('fazenda_id');

        return is_string($v) ? $v : null;
    }
}
