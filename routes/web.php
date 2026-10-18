<?php

declare(strict_types=1);

use App\Http\Controllers\HealthController;
use App\Http\Controllers\StyleguideController;
use Illuminate\Support\Facades\Route;

Route::get('/healthz', [HealthController::class, 'healthz']);
Route::get('/readyz', [HealthController::class, 'readyz']);

Route::get('/_styleguide', StyleguideController::class);

Route::get('/', function () {
    return view('welcome');
});
