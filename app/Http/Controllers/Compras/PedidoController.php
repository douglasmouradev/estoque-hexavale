<?php

declare(strict_types=1);

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use HexaVale\Compartilhado\Dominio\UuidV7;
use HexaVale\Compras\Aplicacao\AprovarPedidoCompra;
use HexaVale\Compras\Aplicacao\CriarPedidoCompra;
use HexaVale\Compras\Aplicacao\Portas\PortaPedidoCompra;
use HexaVale\Compras\Aplicacao\ReceberPedidoCompra;
use HexaVale\Compras\Aplicacao\RejeitarPedidoCompra;
use HexaVale\Compras\Aplicacao\SolicitarAprovacaoPedido;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class PedidoController extends Controller
{
    public function index(Request $request, PortaPedidoCompra $pedidos): View
    {
        $fazendaId = $this->fazendaId($request);

        return view('compras.index', [
            'pedidos' => $pedidos->listar($fazendaId),
        ]);
    }

    public function create(Request $request): View
    {
        $fazendaId = $this->fazendaId($request);

        return view('compras.create', [
            'produtos' => Produto::query()->where('fazenda_id', $fazendaId)->orderBy('nome')->get(),
        ]);
    }

    public function store(Request $request, CriarPedidoCompra $criar): RedirectResponse
    {
        $request->validate([
            'fornecedor_nome' => ['nullable', 'string', 'max:255'],
            'observacao' => ['nullable', 'string', 'max:255'],
            'produto_id' => ['required', 'array', 'min:1'],
            'produto_id.*' => ['required', 'string'],
            'quantidade' => ['required', 'array', 'min:1'],
            'quantidade.*' => ['required', 'string'],
            'unidade' => ['nullable', 'array'],
            'unidade.*' => ['nullable', 'string', 'max:8'],
        ]);

        $produtoIds = $request->input('produto_id');
        $quantidades = $request->input('quantidade');
        $unidades = $request->input('unidade', []);
        if (! is_array($produtoIds) || ! is_array($quantidades)) {
            return back()->withErrors(['produto_id' => 'Itens inválidos.']);
        }
        if (! is_array($unidades)) {
            $unidades = [];
        }

        $itens = [];
        foreach (array_values($produtoIds) as $i => $produtoId) {
            if (! is_string($produtoId) && ! is_numeric($produtoId)) {
                return back()->withErrors(['produto_id' => 'Produto inválido.']);
            }
            $qtd = $quantidades[$i] ?? null;
            if (! is_string($qtd) && ! is_numeric($qtd)) {
                return back()->withErrors(['quantidade' => 'Quantidade inválida.']);
            }
            $unidade = $unidades[$i] ?? '';
            $itens[] = [
                'produto_id' => (string) $produtoId,
                'quantidade' => (string) $qtd,
                'unidade' => is_string($unidade) || is_numeric($unidade) ? (string) $unidade : '',
            ];
        }

        $user = $request->user();
        if ($user === null) {
            abort(403);
        }

        $r = $criar->executar(
            $this->fazendaId($request),
            (string) $user->id,
            $request->filled('fornecedor_nome') ? $request->string('fornecedor_nome')->toString() : null,
            $request->filled('observacao') ? $request->string('observacao')->toString() : null,
            $itens,
        );
        if (($r['ok'] ?? false) !== true || ! isset($r['pedido_id'])) {
            return back()->withErrors(['produto_id' => $r['mensagem'] ?? 'Falha ao criar pedido.']);
        }

        return redirect()->route('compras.show', $r['pedido_id'])->with('ok', 'Pedido criado.');
    }

    public function show(Request $request, string $pedido, PortaPedidoCompra $pedidos): View
    {
        $fazendaId = $this->fazendaId($request);
        $dados = $pedidos->obter($fazendaId, $pedido);
        if ($dados === null) {
            abort(404);
        }

        return view('compras.show', [
            'pedido' => $dados,
            'idempotencyKey' => UuidV7::gerar()->valor,
        ]);
    }

    public function solicitar(
        Request $request,
        string $pedido,
        SolicitarAprovacaoPedido $solicitar,
    ): RedirectResponse {
        $user = $request->user();
        if ($user === null) {
            abort(403);
        }
        $r = $solicitar->executar(
            $this->fazendaId($request),
            $pedido,
            (string) $user->id,
        );
        if (($r['ok'] ?? false) !== true) {
            return back()->withErrors(['status' => $r['mensagem'] ?? 'Falha ao solicitar.']);
        }

        return back()->with('ok', 'Pedido enviado para aprovação.');
    }

    public function aprovar(
        Request $request,
        string $pedido,
        AprovarPedidoCompra $aprovar,
    ): RedirectResponse {
        $user = $request->user();
        if ($user === null) {
            abort(403);
        }
        $r = $aprovar->executar(
            $this->fazendaId($request),
            $pedido,
            (string) $user->id,
        );
        if (($r['ok'] ?? false) !== true) {
            return back()->withErrors(['status' => $r['mensagem'] ?? 'Falha ao aprovar.']);
        }

        return back()->with('ok', 'Pedido aprovado.');
    }

    public function rejeitar(
        Request $request,
        string $pedido,
        RejeitarPedidoCompra $rejeitar,
    ): RedirectResponse {
        $request->validate(['motivo' => ['required', 'string', 'max:255']]);
        $user = $request->user();
        if ($user === null) {
            abort(403);
        }
        $r = $rejeitar->executar(
            $this->fazendaId($request),
            $pedido,
            (string) $user->id,
            $request->string('motivo')->toString(),
        );
        if (($r['ok'] ?? false) !== true) {
            return back()->withErrors(['motivo' => $r['mensagem'] ?? 'Falha ao rejeitar.']);
        }

        return back()->with('ok', 'Pedido rejeitado.');
    }

    public function receber(
        Request $request,
        string $pedido,
        ReceberPedidoCompra $receber,
    ): RedirectResponse {
        $request->validate([
            'chave_nfe' => ['required', 'string', 'max:64'],
            'item_id' => ['required', 'array', 'min:1'],
            'item_id.*' => ['required', 'string'],
            'lote_codigo' => ['required', 'array', 'min:1'],
            'lote_codigo.*' => ['required', 'string', 'max:64'],
            'validade' => ['nullable', 'array'],
            'validade.*' => ['nullable', 'date'],
            '_idempotency_key' => ['required', 'string', 'max:64'],
        ]);

        $itemIds = $request->input('item_id');
        $loteCodigos = $request->input('lote_codigo');
        $validades = $request->input('validade', []);
        if (! is_array($itemIds) || ! is_array($loteCodigos)) {
            return back()->withErrors(['chave_nfe' => 'Itens de recebimento inválidos.']);
        }
        if (! is_array($validades)) {
            $validades = [];
        }

        $lotes = [];
        foreach (array_values($itemIds) as $i => $itemId) {
            if (! is_string($itemId) && ! is_numeric($itemId)) {
                return back()->withErrors(['chave_nfe' => 'Item inválido.']);
            }
            $codigo = $loteCodigos[$i] ?? null;
            if (! is_string($codigo) && ! is_numeric($codigo)) {
                return back()->withErrors(['chave_nfe' => 'Lote inválido.']);
            }
            $validade = $validades[$i] ?? null;
            $validadeStr = null;
            if (is_string($validade) && $validade !== '') {
                $validadeStr = $validade;
            }
            $lotes[] = [
                'item_id' => (string) $itemId,
                'lote_codigo' => (string) $codigo,
                'validade' => $validadeStr,
            ];
        }

        $user = $request->user();
        if ($user === null) {
            abort(403);
        }

        $r = $receber->executar(
            $this->fazendaId($request),
            $pedido,
            $request->string('chave_nfe')->toString(),
            $lotes,
            (string) $user->id,
            $request->string('_idempotency_key')->toString(),
        );
        if (($r['ok'] ?? false) !== true) {
            return back()->withErrors(['chave_nfe' => $r['mensagem'] ?? 'Falha no recebimento.']);
        }

        return redirect()->route('estoque.saldo')->with('ok', 'Pedido recebido e estoque atualizado.');
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
