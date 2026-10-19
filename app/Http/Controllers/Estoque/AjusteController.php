<?php

declare(strict_types=1);

namespace App\Http\Controllers\Estoque;

use App\Http\Controllers\Controller;
use App\Models\Lote;
use HexaVale\Compartilhado\Dominio\UuidV7;
use HexaVale\Estoque\Aplicacao\Portas\PortaLedgerEstoque;
use HexaVale\Estoque\Aplicacao\RegistrarAjusteInventario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class AjusteController extends Controller
{
    public function create(Request $request, PortaLedgerEstoque $ledger): View
    {
        $fazendaId = $this->fazendaId($request);

        return view('estoque.ajuste', [
            'lotes' => Lote::query()->with('produto')->where('fazenda_id', $fazendaId)->orderBy('codigo')->get(),
            'saldos' => $ledger->listarSaldos($fazendaId),
            'idempotencyKey' => UuidV7::gerar()->valor,
        ]);
    }

    public function store(Request $request, RegistrarAjusteInventario $ajustar): RedirectResponse
    {
        $request->validate([
            'lote_id' => ['required', 'string'],
            'contagem' => ['required', 'string'],
            'motivo' => ['required', 'string', 'max:255'],
            'unidade' => ['nullable', 'string', 'max:8'],
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

        $resultado = $ajustar->executar(
            $fazendaId,
            $loteId,
            $request->string('contagem')->toString(),
            $unidade,
            $unidadeBase,
            $request->string('motivo')->toString(),
            $request->user() !== null ? (string) $request->user()->id : null,
            $request->string('_idempotency_key')->toString(),
        );

        if (($resultado['ok'] ?? false) !== true) {
            return back()->withErrors(['contagem' => $resultado['mensagem'] ?? 'Falha no ajuste.']);
        }

        return redirect()->route('estoque.saldo')->with('ok', 'Ajuste de inventário registrado.');
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
