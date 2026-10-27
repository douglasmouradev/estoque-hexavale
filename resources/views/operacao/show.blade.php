@extends('layouts.app')

@section('title', 'Ordem · HexaVale')
@section('heading', 'Ordem agrícola')
@section('lead')
    Status: {{ $ordem['status'] }} · {{ $ordem['tipo'] }} · talhão {{ $ordem['talhao'] }}
@endsection
@section('breadcrumb')
    <a href="{{ route('operacao.index') }}">Ordens</a> / Detalhe
@endsection

@section('content')
@if (session('ok'))
    <p class="hx-alert hx-alert--ok" role="status">{{ session('ok') }}</p>
@endif
@error('status')
    <p class="hx-alert hx-alert--er">{{ $message }}</p>
@enderror

<section class="hx-card" style="margin-bottom:var(--space-6);">
    <p><strong>Área planejada:</strong> {{ $ordem['area_planejada_ha'] }} ha</p>
    @if ($ordem['area_tratada_ha'])
        <p><strong>Área tratada:</strong> {{ $ordem['area_tratada_ha'] }} ha</p>
    @endif
    @if ($ordem['calda_l_ha'])
        <p><strong>Calda:</strong> {{ $ordem['calda_l_ha'] }} L/ha</p>
    @endif
    @if ($ordem['motivo_cancelamento'])
        <p><strong>Cancelamento:</strong> {{ $ordem['motivo_cancelamento'] }}</p>
    @endif
    <div class="hx-table-wrap">
        <table class="hx-table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Lote</th>
                    <th>Dose</th>
                    <th>Qtd calculada</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ordem['itens'] as $item)
                    <tr>
                        <td>{{ $item['produto'] }}</td>
                        <td class="mono">{{ $item['lote'] }}</td>
                        <td class="num">{{ $item['dose'] }}</td>
                        <td class="num">{{ $item['quantidade'] }} {{ $item['unidade'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

@if ($ordem['status'] === 'rascunho')
<section class="hx-card hx-card--narrow">
    <form method="post" action="{{ route('operacao.liberar', $ordem['id']) }}">
        @csrf
        <input type="hidden" name="_idempotency_key" value="{{ $idempotencyKey }}">
        <button type="submit" class="hx-btn">Liberar (reservar estoque)</button>
    </form>
</section>
@endif

@if ($ordem['status'] === 'liberada')
<section class="hx-card hx-card--narrow">
    <form method="post" action="{{ route('operacao.baixar', $ordem['id']) }}" class="hx-form" style="margin-bottom:var(--space-4);">
        @csrf
        <input type="hidden" name="_idempotency_key" value="{{ $idempotencyKey }}">
        <label class="hx-field">
            Área tratada (ha) — ≤ planejada (RN-06)
            <input type="text" name="area_tratada_ha" class="tabular" required inputmode="decimal" value="{{ $ordem['area_planejada_ha'] }}">
        </label>
        @error('area_tratada_ha')
            <p class="hx-field__erro">{{ $message }}</p>
        @enderror
        <button type="submit" class="hx-btn">Confirmar baixa</button>
    </form>
</section>
@endif

@if (in_array($ordem['status'], ['rascunho', 'liberada'], true))
<section class="hx-card hx-card--narrow">
    <form method="post" action="{{ route('operacao.cancelar', $ordem['id']) }}" class="hx-form">
        @csrf
        <input type="hidden" name="_idempotency_key" value="{{ $idempotencyKeyCancel }}">
        <label class="hx-field">
            Motivo do cancelamento (RN-01)
            <input type="text" name="motivo" required maxlength="255">
        </label>
        @error('motivo')
            <p class="hx-field__erro">{{ $message }}</p>
        @enderror
        <button type="submit" class="hx-btn hx-btn--ghost">Cancelar ordem</button>
    </form>
</section>
@endif
@endsection
