<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\UsuarioPapelFazenda;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureFazendaContext
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user === null) {
            return redirect()->route('entrar');
        }

        $fazendaId = $request->session()->get('fazenda_id');
        if (! is_string($fazendaId) || $fazendaId === '') {
            $primeiro = UsuarioPapelFazenda::query()->where('user_id', $user->id)->value('fazenda_id');
            if (! is_string($primeiro)) {
                abort(403, 'Usuário sem fazenda vinculada.');
            }
            $request->session()->put('fazenda_id', $primeiro);
            $fazendaId = $primeiro;
        }

        $ok = UsuarioPapelFazenda::query()
            ->where('user_id', $user->id)
            ->where('fazenda_id', $fazendaId)
            ->exists();

        if (! $ok) {
            abort(403, 'Acesso negado a esta fazenda.');
        }

        $request->attributes->set('fazenda_id', $fazendaId);

        return $next($request);
    }
}
