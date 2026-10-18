<?php

declare(strict_types=1);

namespace App\Http\Controllers\Estoque;

use App\Http\Controllers\Controller;
use App\Models\Lote;
use HexaVale\Estoque\Aplicacao\Portas\PortaLedgerEstoque;
use HexaVale\Estoque\Aplicacao\RegistrarSaida;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class SaidaController extends Controller
{
    public function create(Request $request, PortaLedgerEstoque $ledger): View
    {
        $fazendaId = $this->fazendaId($request);
        $lotes = Lote::query()->with('produto')->where('fazenda_id', $fazendaId)->orderBy('codigo')->get();

        return view('estoque.saida', [
            'lotes' => $lotes,
            'saldos' => $ledger->listarSaldos($fazendaId),
        ]);
    }

    public function store(Request $request, RegistrarSaida $sair): RedirectResponse
    {
        $request->validate([
            'lote_id' => ['required', 'string'],
            'quantidade' => ['required', 'string'],
            'motivo' => ['nullable', 'string', 'max:255'],
        ]);

        $fazendaId = $this->fazendaId($request);
        $loteId = $request->string('lote_id')->toString();
        $lote = Lote::query()->with('produto')->where('fazenda_id', $fazendaId)->where('id', $loteId)->first();
        if ($lote === null || $lote->produto === null) {
            return back()->withErrors(['lote_id' => 'Lote inválido.']);
        }

        $resultado = $sair->executar(
            $fazendaId,
            $loteId,
            $request->string('quantidade')->toString(),
            (string) $lote->produto->unidade_base,
            $request->input('motivo') !== null ? $request->string('motivo')->toString() : null,
            $request->user() !== null ? (string) $request->user()->id : null,
            $request->header('Idempotency-Key'),
        );

        if (($resultado['ok'] ?? false) !== true) {
            return back()->withErrors(['quantidade' => $resultado['mensagem'] ?? 'Falha na saída.']);
        }

        return redirect()->route('estoque.saldo')->with('ok', 'Saída registrada.');
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
