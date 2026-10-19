@extends('layouts.app')

@section('title', 'Saída · HexaVale')
@section('heading', 'Saída de estoque')
@section('lead', 'Não permite saldo disponível negativo (RN-07).')
@section('breadcrumb')
    <a href="{{ route('estoque.saldo') }}">Saldo</a> / Saída
@endsection

@section('content')
<section class="hx-card hx-card--narrow">
    <form method="post" action="{{ route('estoque.saida.store') }}" class="hx-form">
        @csrf
        <input type="hidden" name="_idempotency_key" value="{{ $idempotencyKey }}">
        <label class="hx-field">
            Lote
            <select name="lote_id" required>
                @foreach ($lotes as $lote)
                    <option value="{{ $lote->id }}">{{ $lote->produto?->nome }} · {{ $lote->codigo }} ({{ $lote->produto?->unidade_base }})</option>
                @endforeach
            </select>
        </label>
        <label class="hx-field">
            Quantidade
            <input type="text" name="quantidade" class="tabular" required inputmode="decimal" placeholder="0.000">
        </label>
        <label class="hx-field">
            Unidade (opcional)
            <input type="text" name="unidade" placeholder="kg, g, L, mL" maxlength="8">
        </label>
        <label class="hx-field">
            Motivo
            <input type="text" name="motivo" maxlength="255">
        </label>
        @error('quantidade')
            <p class="hx-field__erro">{{ $message }}</p>
        @enderror
        @error('lote_id')
            <p class="hx-field__erro">{{ $message }}</p>
        @enderror
        <button type="submit" class="hx-btn">Registrar saída</button>
    </form>
</section>
@endsection
