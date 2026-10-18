<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use HexaVale\Identidade\Dominio\Permissao;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsurePermission
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string $permissao): Response
    {
        $user = $request->user();
        if (! $user instanceof User) {
            abort(403);
        }

        $fazendaAttr = $request->attributes->get('fazenda_id');
        $fazendaSess = $request->session()->get('fazenda_id');
        $fazendaId = is_string($fazendaAttr) ? $fazendaAttr : (is_string($fazendaSess) ? $fazendaSess : null);
        if ($fazendaId === null) {
            abort(403);
        }

        $enum = Permissao::tryFrom($permissao);
        if ($enum === null || ! $user->temPermissaoNaFazenda($fazendaId, $enum)) {
            abort(403, 'Permissão insuficiente.');
        }

        return $next($request);
    }
}
