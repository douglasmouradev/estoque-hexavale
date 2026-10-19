<?php

declare(strict_types=1);

namespace App\Http\Controllers\Estoque;

use App\Http\Controllers\Controller;
use App\Models\Lote;
use HexaVale\Compartilhado\Dominio\UuidV7;
use HexaVale\Estoque\Aplicacao\Portas\PortaLedgerEstoque;
use HexaVale\Estoque\Aplicacao\RegistrarEntrada;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class EntradaController extends Controller
{
    public function create(Request $request, PortaLedgerEstoque $ledger): View
    {
        $fazendaId = $this->fazendaId($request);
        $lotes = Lote::query()->with('produto')->where('fazenda_id', $fazendaId)->orderBy('codigo')->get();

        return view('estoque.entrada', [
            'lotes' => $lotes,
            'saldos' => $ledger->listarSaldos($fazendaId),
            'idempotencyKey' => UuidV7::gerar()->valor,
        ]);
    }

    public function store(Request $request, RegistrarEntrada $entrar): RedirectResponse
    {
        $request->validate([
            'lote_id' => ['required', 'string'],
            'quantidade' => ['required', 'string'],
            'unidade' => ['nullable', 'string', 'max:8'],
            'motivo' => ['nullable', 'string', 'max:255'],
            '_idempotency_key' => ['required', 'string', 'max:64'],
        ]);

        $fazendaId = $this->fazendaId($request);
        $loteId = $request->string('lote_id')->toString();
        $lote = Lote::query()->with('produto')->where('fazenda_id', $fazendaId)->where('id', $loteId)->first();
        if ($lote === null || $lote->produto === null) {
            return back()->withErrors(['lote_id' => 'Lote inválido.']);
        }

        $unidadeBase = (string) $lote->produto->unidade_base;
        $unidade = $request->filled('unidade') ? $request->string('unidade')->toString() : $unidadeBase;

        $resultado = $entrar->executar(
            $fazendaId,
            $loteId,
            $request->string('quantidade')->toString(),
            $unidade,
            $unidadeBase,
            $request->input('motivo') !== null ? $request->string('motivo')->toString() : null,
            $request->user() !== null ? (string) $request->user()->id : null,
            $request->string('_idempotency_key')->toString(),
        );

        if (($resultado['ok'] ?? false) !== true) {
            return back()->withErrors(['quantidade' => $resultado['mensagem'] ?? 'Falha na entrada.']);
        }

        return redirect()->route('estoque.saldo')->with('ok', 'Entrada registrada.');
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
