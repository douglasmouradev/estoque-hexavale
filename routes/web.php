<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\UsuariosController;
use App\Http\Controllers\Auth\EntrarController;
use App\Http\Controllers\Auth\SairController;
use App\Http\Controllers\Compras\PedidoController;
use App\Http\Controllers\ContaController;
use App\Http\Controllers\Estoque\AjusteController;
use App\Http\Controllers\Estoque\EntradaController;
use App\Http\Controllers\Estoque\ReservaController;
use App\Http\Controllers\Estoque\SaidaController;
use App\Http\Controllers\Estoque\SaldoController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\Operacao\OrdemController;
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
        Route::get('/estoque/reserva', [ReservaController::class, 'create'])->name('estoque.reserva');
        Route::post('/estoque/reserva', [ReservaController::class, 'store'])
            ->middleware(RequireIdempotencyKey::class)
            ->name('estoque.reserva.store');
        Route::get('/estoque/reserva/liberar', [ReservaController::class, 'createLiberar'])->name('estoque.reserva.liberar');
        Route::post('/estoque/reserva/liberar', [ReservaController::class, 'storeLiberar'])
            ->middleware(RequireIdempotencyKey::class)
            ->name('estoque.reserva.liberar.store');
    });

    Route::middleware(EnsurePermission::class.':compras.solicitar')->group(function (): void {
        Route::get('/compras/novo', [PedidoController::class, 'create'])->name('compras.create');
        Route::post('/compras', [PedidoController::class, 'store'])->name('compras.store');
        Route::post('/compras/{pedido}/solicitar', [PedidoController::class, 'solicitar'])->name('compras.solicitar');
    });

    Route::middleware(EnsurePermission::class.':compras.ler')->group(function (): void {
        Route::get('/compras', [PedidoController::class, 'index'])->name('compras.index');
        Route::get('/compras/{pedido}', [PedidoController::class, 'show'])->name('compras.show');
    });

    Route::middleware(EnsurePermission::class.':compras.aprovar')->group(function (): void {
        Route::post('/compras/{pedido}/aprovar', [PedidoController::class, 'aprovar'])->name('compras.aprovar');
        Route::post('/compras/{pedido}/rejeitar', [PedidoController::class, 'rejeitar'])->name('compras.rejeitar');
    });

    Route::middleware(EnsurePermission::class.':compras.receber')->group(function (): void {
        Route::post('/compras/{pedido}/receber', [PedidoController::class, 'receber'])
            ->middleware(RequireIdempotencyKey::class)
            ->name('compras.receber');
    });

    Route::middleware(EnsurePermission::class.':operacao.criar')->group(function (): void {
        Route::get('/operacao/novo', [OrdemController::class, 'create'])->name('operacao.create');
        Route::post('/operacao', [OrdemController::class, 'store'])->name('operacao.store');
    });

    Route::middleware(EnsurePermission::class.':operacao.ler')->group(function (): void {
        Route::get('/operacao', [OrdemController::class, 'index'])->name('operacao.index');
        Route::get('/operacao/{ordem}', [OrdemController::class, 'show'])->name('operacao.show');
    });

    Route::middleware(EnsurePermission::class.':operacao.liberar')->group(function (): void {
        Route::post('/operacao/{ordem}/liberar', [OrdemController::class, 'liberar'])
            ->middleware(RequireIdempotencyKey::class)
            ->name('operacao.liberar');
        Route::post('/operacao/{ordem}/cancelar', [OrdemController::class, 'cancelar'])
            ->middleware(RequireIdempotencyKey::class)
            ->name('operacao.cancelar');
    });

    Route::middleware(EnsurePermission::class.':operacao.baixar')->group(function (): void {
        Route::post('/operacao/{ordem}/baixar', [OrdemController::class, 'baixar'])
            ->middleware(RequireIdempotencyKey::class)
            ->name('operacao.baixar');
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
