<?php

declare(strict_types=1);

namespace App\Http\Controllers\Operacao;

use App\Http\Controllers\Controller;
use App\Models\Lote;
use App\Models\Talhao;
use HexaVale\Compartilhado\Dominio\UuidV7;
use HexaVale\OperacaoAgricola\Aplicacao\BaixarOrdemAgricola;
use HexaVale\OperacaoAgricola\Aplicacao\CancelarOrdemAgricola;
use HexaVale\OperacaoAgricola\Aplicacao\CriarOrdemAgricola;
use HexaVale\OperacaoAgricola\Aplicacao\LiberarOrdemAgricola;
use HexaVale\OperacaoAgricola\Aplicacao\Portas\PortaOrdemAgricola;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class OrdemController extends Controller
{
    public function index(Request $request, PortaOrdemAgricola $ordens): View
    {
        return view('operacao.index', [
            'ordens' => $ordens->listar($this->fazendaId($request)),
        ]);
    }

    public function create(Request $request): View
    {
        $fazendaId = $this->fazendaId($request);

        return view('operacao.create', [
            'talhoes' => Talhao::query()->where('fazenda_id', $fazendaId)->orderBy('codigo')->get(),
            'lotes' => Lote::query()->with('produto')->where('fazenda_id', $fazendaId)->orderBy('codigo')->get(),
        ]);
    }

    public function store(Request $request, CriarOrdemAgricola $criar): RedirectResponse
    {
        $request->validate([
            'talhao_id' => ['required', 'string'],
            'tipo' => ['required', 'string', 'in:pulverizacao,adubacao'],
            'area_planejada_ha' => ['required', 'string'],
            'calda_l_ha' => ['nullable', 'string'],
            'observacao' => ['nullable', 'string', 'max:255'],
            'lote_id' => ['required', 'string'],
            'dose' => ['required', 'string'],
        ]);

        $fazendaId = $this->fazendaId($request);
        $lote = Lote::query()->with('produto')->where('fazenda_id', $fazendaId)->where('id', $request->string('lote_id')->toString())->first();
        if ($lote === null || $lote->produto === null) {
            return back()->withErrors(['lote_id' => 'Lote inválido.']);
        }

        $user = $request->user();
        if ($user === null) {
            abort(403);
        }

        $r = $criar->executar(
            $fazendaId,
            $request->string('talhao_id')->toString(),
            $request->string('tipo')->toString(),
            $request->string('area_planejada_ha')->toString(),
            $request->filled('calda_l_ha') ? $request->string('calda_l_ha')->toString() : null,
            $request->filled('observacao') ? $request->string('observacao')->toString() : null,
            (string) $user->id,
            [[
                'lote_id' => (string) $lote->id,
                'produto_id' => (string) $lote->produto_id,
                'dose' => $request->string('dose')->toString(),
                'unidade' => (string) $lote->produto->unidade_base,
            ]],
        );
        if (($r['ok'] ?? false) !== true || ! isset($r['ordem_id'])) {
            return back()->withErrors(['dose' => $r['mensagem'] ?? 'Falha ao criar ordem.']);
        }

        return redirect()->route('operacao.show', $r['ordem_id'])->with('ok', 'Ordem criada (rascunho).');
    }

    public function show(Request $request, string $ordem, PortaOrdemAgricola $ordens): View
    {
        $dados = $ordens->obter($this->fazendaId($request), $ordem);
        if ($dados === null) {
            abort(404);
        }

        return view('operacao.show', [
            'ordem' => $dados,
            'idempotencyKey' => UuidV7::gerar()->valor,
            'idempotencyKeyCancel' => UuidV7::gerar()->valor,
        ]);
    }

    public function liberar(
        Request $request,
        string $ordem,
        LiberarOrdemAgricola $liberar,
    ): RedirectResponse {
        $request->validate(['_idempotency_key' => ['required', 'string', 'max:64']]);
        $user = $request->user();
        if ($user === null) {
            abort(403);
        }
        $r = $liberar->executar(
            $this->fazendaId($request),
            $ordem,
            (string) $user->id,
            $request->string('_idempotency_key')->toString(),
        );
        if (($r['ok'] ?? false) !== true) {
            return back()->withErrors(['status' => $r['mensagem'] ?? 'Falha ao liberar.']);
        }

        return back()->with('ok', 'Ordem liberada e estoque reservado.');
    }

    public function baixar(
        Request $request,
        string $ordem,
        BaixarOrdemAgricola $baixar,
    ): RedirectResponse {
        $request->validate([
            'area_tratada_ha' => ['required', 'string'],
            '_idempotency_key' => ['required', 'string', 'max:64'],
        ]);
        $user = $request->user();
        if ($user === null) {
            abort(403);
        }
        $r = $baixar->executar(
            $this->fazendaId($request),
            $ordem,
            $request->string('area_tratada_ha')->toString(),
            (string) $user->id,
            $request->string('_idempotency_key')->toString(),
        );
        if (($r['ok'] ?? false) !== true) {
            return back()->withErrors(['area_tratada_ha' => $r['mensagem'] ?? 'Falha na baixa.']);
        }

        return redirect()->route('estoque.saldo')->with('ok', 'Ordem baixada; estoque consumido.');
    }

    public function cancelar(
        Request $request,
        string $ordem,
        CancelarOrdemAgricola $cancelar,
    ): RedirectResponse {
        $request->validate([
            'motivo' => ['required', 'string', 'max:255'],
            '_idempotency_key' => ['required', 'string', 'max:64'],
        ]);
        $user = $request->user();
        if ($user === null) {
            abort(403);
        }
        $r = $cancelar->executar(
            $this->fazendaId($request),
            $ordem,
            $request->string('motivo')->toString(),
            (string) $user->id,
            $request->string('_idempotency_key')->toString(),
        );
        if (($r['ok'] ?? false) !== true) {
            return back()->withErrors(['motivo' => $r['mensagem'] ?? 'Falha ao cancelar.']);
        }

        return back()->with('ok', 'Ordem cancelada.');
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
