<?php

declare(strict_types=1);

namespace App\Http\Controllers\Estoque;

use App\Http\Controllers\Controller;
use HexaVale\Estoque\Aplicacao\Portas\PortaLedgerEstoque;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class SaldoController extends Controller
{
    public function __invoke(Request $request, PortaLedgerEstoque $ledger): View
    {
        $fazendaId = $this->fazendaId($request);
        $saldos = $ledger->listarSaldos($fazendaId);

        return view('estoque.saldo', [
            'saldos' => $saldos,
            'fazendaId' => $fazendaId,
        ]);
    }

    private function fazendaId(Request $request): string
    {
        $v = $request->attributes->get('fazenda_id') ?? $request->session()->get('fazenda_id');
        if (! is_string($v) || $v === '') {
            abort(403);
        }

        return $v;
    }
}
