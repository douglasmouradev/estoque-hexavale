<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** RN-18 — operações mutáveis de estoque exigem Idempotency-Key. */
final class RequireIdempotencyKey
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('Idempotency-Key');
        if (! is_string($key) || trim($key) === '') {
            $key = $request->input('_idempotency_key');
        }
        if (! is_string($key) || trim($key) === '') {
            abort(400, 'Cabeçalho Idempotency-Key (ou _idempotency_key) é obrigatório (RN-18).');
        }
        $request->attributes->set('idempotency_key', trim($key));

        return $next($request);
    }
}
