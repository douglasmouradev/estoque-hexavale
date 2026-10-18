<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;

final class StyleguideController extends Controller
{
    public function __invoke(): View|Response
    {
        $env = app()->environment();
        if ($env !== 'local' && $env !== 'testing') {
            abort(404);
        }

        return view('styleguide');
    }
}
