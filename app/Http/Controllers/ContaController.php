<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use HexaVale\Auditoria\Aplicacao\PortaRegistrarAuditoria;
use HexaVale\Identidade\Aplicacao\Portas\PortaTotp;
use HexaVale\Privacidade\Dominio\CifradorCampo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ContaController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();
        if ($user === null) {
            abort(403);
        }

        return view('conta.show', [
            'user' => $user,
            'fazendaId' => $this->fazendaDaSessao($request),
        ]);
    }

    public function ativarTotp(Request $request, PortaTotp $totp, CifradorCampo $cifrador, PortaRegistrarAuditoria $auditoria): View|RedirectResponse
    {
        $user = $request->user();
        if ($user === null) {
            abort(403);
        }

        if ($request->isMethod('get')) {
            $segredo = $totp->gerarSegredo();
            $request->session()->put('totp_setup_secret', $segredo);
            $uri = $totp->uriProvisoria($segredo, (string) $user->email);

            return view('conta.totp-setup', [
                'uri' => $uri,
                'segredo' => $segredo,
            ]);
        }

        $request->validate(['codigo' => ['required', 'string', 'size:6']]);
        $codigo = $request->string('codigo')->toString();
        $segredo = $request->session()->get('totp_setup_secret');
        if (! is_string($segredo) || ! $totp->verificar($segredo, $codigo)) {
            return back()->withErrors(['codigo' => 'Código inválido.']);
        }

        $user->mfa_secret_cifrado = $cifrador->cifrar($segredo);
        $user->mfa_enabled = true;
        $user->save();
        $request->session()->forget('totp_setup_secret');

        $auditoria->registrar(
            $this->fazendaDaSessao($request),
            (string) $user->id,
            'MFA_ATIVADO',
            'usuario',
            (string) $user->id,
            null,
            ['mfa_enabled' => true],
            null,
            $request->ip(),
            $request->userAgent(),
        );

        return redirect()->route('conta')->with('ok', 'MFA TOTP ativado.');
    }

    private function fazendaDaSessao(Request $request): ?string
    {
        $v = $request->session()->get('fazenda_id');

        return is_string($v) ? $v : null;
    }
}
