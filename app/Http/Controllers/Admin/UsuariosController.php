<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UsuarioPapelFazenda;
use HexaVale\Auditoria\Aplicacao\PortaRegistrarAuditoria;
use HexaVale\Compartilhado\Dominio\UuidV7;
use HexaVale\Privacidade\Dominio\CifradorCampo;
use HexaVale\Privacidade\Dominio\MascaradorPii;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

final class UsuariosController extends Controller
{
    public function index(Request $request, MascaradorPii $mascara, CifradorCampo $cifrador): View
    {
        $fazendaId = $this->fazendaObrigatoria($request);
        $userIds = UsuarioPapelFazenda::query()->where('fazenda_id', $fazendaId)->pluck('user_id');

        /** @var list<array{id: int, name: string, email: string, cpf: string}> $usuarios */
        $usuarios = [];
        foreach (User::query()->whereIn('id', $userIds)->orderBy('name')->get() as $u) {
            $cpfCifrado = $u->cpf_cifrado;
            $cpf = is_string($cpfCifrado) && $cpfCifrado !== ''
                ? $mascara->cpf($cifrador->decifrar($cpfCifrado))
                : '—';
            $usuarios[] = [
                'id' => (int) $u->id,
                'name' => (string) $u->name,
                'email' => $mascara->email((string) $u->email),
                'cpf' => $cpf,
            ];
        }

        return view('admin.usuarios', ['usuarios' => $usuarios]);
    }

    public function revelarCpf(
        Request $request,
        User $user,
        CifradorCampo $cifrador,
        PortaRegistrarAuditoria $auditoria,
    ): RedirectResponse {
        $request->validate(['motivo' => ['required', 'string', 'min:5', 'max:255']]);
        $motivo = $request->string('motivo')->toString();
        $fazendaId = $this->fazendaObrigatoria($request);
        $ator = $request->user();
        if ($ator === null) {
            abort(403);
        }

        $pertence = UsuarioPapelFazenda::query()
            ->where('fazenda_id', $fazendaId)
            ->where('user_id', $user->id)
            ->exists();
        if (! $pertence) {
            abort(403, 'Usuário fora da fazenda.');
        }

        $cpfCifrado = $user->cpf_cifrado;
        $cpf = is_string($cpfCifrado) && $cpfCifrado !== ''
            ? $cifrador->decifrar($cpfCifrado)
            : null;

        DB::table('acessos_pii')->insert([
            'id' => UuidV7::gerar()->valor,
            'ator_id' => $ator->id,
            'titular_id' => $user->id,
            'campo' => 'cpf',
            'motivo' => $motivo,
            'fazenda_id' => $fazendaId,
            'acessado_em' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $auditoria->registrar(
            $fazendaId,
            (string) $ator->id,
            'PII_REVELAR',
            'usuario',
            (string) $user->id,
            null,
            ['campo' => 'cpf'],
            $motivo,
            $request->ip(),
            $request->userAgent(),
        );

        return back()->with('pii_revelado', [
            'user_id' => $user->id,
            'cpf' => $cpf,
        ]);
    }

    private function fazendaObrigatoria(Request $request): string
    {
        $v = $request->session()->get('fazenda_id');
        if (! is_string($v) || $v === '') {
            abort(403, 'Fazenda não definida.');
        }

        return $v;
    }
}
