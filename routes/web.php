<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\UsuariosController;
use App\Http\Controllers\Auth\EntrarController;
use App\Http\Controllers\Auth\SairController;
use App\Http\Controllers\ContaController;
use App\Http\Controllers\Estoque\AjusteController;
use App\Http\Controllers\Estoque\EntradaController;
use App\Http\Controllers\Estoque\SaidaController;
use App\Http\Controllers\Estoque\SaldoController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\PainelController;
use App\Http\Controllers\StyleguideController;
use App\Http\Middleware\EnsureFazendaContext;
use App\Http\Middleware\EnsurePermission;
use App\Http\Middleware\RequireIdempotencyKey;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/healthz', [HealthController::class, 'healthz']);
Route::get('/readyz', [HealthController::class, 'readyz']);
Route::get('/_styleguide', StyleguideController::class)->name('styleguide');

Route::middleware('guest')->group(function (): void {
    Route::get('/entrar', [EntrarController::class, 'formulario'])->name('entrar');
    Route::post('/entrar', [EntrarController::class, 'autenticar'])->name('entrar.auth');
    Route::post('/entrar/totp', [EntrarController::class, 'verificarTotp'])->name('entrar.totp');
});

Route::post('/sair', SairController::class)->middleware('auth')->name('sair');

Route::middleware(['auth', EnsureFazendaContext::class])->group(function (): void {
    Route::get('/app', PainelController::class)->name('painel');

    Route::get('/conta', [ContaController::class, 'show'])->name('conta');
    Route::match(['get', 'post'], '/conta/totp', [ContaController::class, 'ativarTotp'])->name('conta.totp');

    Route::middleware(EnsurePermission::class.':estoque.ler')->group(function (): void {
        Route::get('/estoque/saldo', SaldoController::class)->name('estoque.saldo');
    });

    Route::middleware(EnsurePermission::class.':estoque.movimentar')->group(function (): void {
        Route::get('/estoque/entrada', [EntradaController::class, 'create'])->name('estoque.entrada');
        Route::post('/estoque/entrada', [EntradaController::class, 'store'])
            ->middleware(RequireIdempotencyKey::class)
            ->name('estoque.entrada.store');
        Route::get('/estoque/saida', [SaidaController::class, 'create'])->name('estoque.saida');
        Route::post('/estoque/saida', [SaidaController::class, 'store'])
            ->middleware(RequireIdempotencyKey::class)
            ->name('estoque.saida.store');
        Route::get('/estoque/ajuste', [AjusteController::class, 'create'])->name('estoque.ajuste');
        Route::post('/estoque/ajuste', [AjusteController::class, 'store'])
            ->middleware(RequireIdempotencyKey::class)
            ->name('estoque.ajuste.store');
    });

    Route::middleware(EnsurePermission::class.':admin.usuarios')->group(function (): void {
        Route::get('/admin/usuarios', [UsuariosController::class, 'index'])->name('admin.usuarios');
    });

    Route::middleware(EnsurePermission::class.':pii.revelar')->group(function (): void {
        Route::post('/admin/usuarios/{user}/revelar-cpf', [UsuariosController::class, 'revelarCpf'])->name('admin.usuarios.revelar');
    });
});

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('painel')
        : redirect()->route('entrar');
});
