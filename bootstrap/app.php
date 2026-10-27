<?php

use App\Http\Middleware\EnsureFazendaContext;
use App\Http\Middleware\EnsurePermission;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Cloudflare Tunnel / reverse proxy
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'fazenda' => EnsureFazendaContext::class,
            'perm' => EnsurePermission::class,
        ]);
        $middleware->redirectGuestsTo('/entrar');
        $middleware->redirectUsersTo('/conta');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
