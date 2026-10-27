@extends('layouts.app')

@section('title', 'Pedido · HexaVale')
@section('heading', 'Pedido de compra')
@section('lead')
    Status: {{ $pedido['status'] }}
@endsection

@section('breadcrumb')
    <a href="{{ route('compras.index') }}">Compras</a> / Detalhe
@endsection

@section('content')
@if (session('ok'))
    <p class="hx-alert hx-alert--ok" role="status">{{ session('ok') }}</p>
@endif
@error('status')
    <p class="hx-alert hx-alert--er">{{ $message }}</p>
@enderror
@error('chave_nfe')
    <p class="hx-alert hx-alert--er">{{ $message }}</p>
@enderror

<section class="hx-card" style="margin-bottom:var(--space-6);">
    <p><strong>Fornecedor:</strong> {{ $pedido['fornecedor_nome'] ?? '—' }}</p>
    <p><strong>Observação:</strong> {{ $pedido['observacao'] ?? '—' }}</p>
    @if ($pedido['chave_nfe'])
        <p><strong>Chave NF-e:</strong> <span class="mono">{{ $pedido['chave_nfe'] }}</span></p>
    @endif
    @if ($pedido['motivo_rejeicao'])
        <p><strong>Rejeição:</strong> {{ $pedido['motivo_rejeicao'] }}</p>
    @endif
    <div class="hx-table-wrap">
        <table class="hx-table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Qtd</th>
                    <th>Unidade</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pedido['itens'] as $item)
                    <tr>
                        <td>{{ $item['produto'] }}</td>
                        <td class="num">{{ $item['quantidade'] }}</td>
                        <td>{{ $item['unidade'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

@if ($pedido['status'] === 'rascunho')
<section class="hx-card hx-card--narrow">
    <form method="post" action="{{ route('compras.solicitar', $pedido['id']) }}">
        @csrf
        <button type="submit" class="hx-btn">Enviar para aprovação</button>
    </form>
</section>
@endif

@if ($pedido['status'] === 'solicitado')
<section class="hx-card hx-card--narrow">
    <form method="post" action="{{ route('compras.aprovar', $pedido['id']) }}" style="margin-bottom:var(--space-4);">
        @csrf
        <button type="submit" class="hx-btn">Aprovar</button>
    </form>
    <form method="post" action="{{ route('compras.rejeitar', $pedido['id']) }}" class="hx-form">
        @csrf
        <label class="hx-field">
            Motivo da rejeição
            <input type="text" name="motivo" required maxlength="255">
        </label>
        @error('motivo')
            <p class="hx-field__erro">{{ $message }}</p>
        @enderror
        <button type="submit" class="hx-btn hx-btn--ghost">Rejeitar</button>
    </form>
</section>
@endif

@if ($pedido['status'] === 'aprovado')
<section class="hx-card hx-card--narrow">
    <form method="post" action="{{ route('compras.receber', $pedido['id']) }}" class="hx-form">
        @csrf
        <input type="hidden" name="_idempotency_key" value="{{ $idempotencyKey }}">
        <label class="hx-field">
            Chave NF-e (44 dígitos)
            <input type="text" name="chave_nfe" required maxlength="64" class="mono" inputmode="numeric">
        </label>
        @foreach ($pedido['itens'] as $item)
            <fieldset class="hx-field">
                <legend>{{ $item['produto'] }} · {{ $item['quantidade'] }} {{ $item['unidade'] }}</legend>
                <input type="hidden" name="item_id[]" value="{{ $item['id'] }}">
                <label class="hx-field">
                    Código do lote
                    <input type="text" name="lote_codigo[]" required maxlength="64">
                </label>
                <label class="hx-field">
                    Validade (opcional)
                    <input type="date" name="validade[]">
                </label>
            </fieldset>
        @endforeach
        <button type="submit" class="hx-btn">Receber e entrar no estoque</button>
    </form>
</section>
@endif
@endsection
