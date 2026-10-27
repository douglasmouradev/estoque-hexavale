@extends('layouts.app')

@section('title', 'Reserva · HexaVale')
@section('heading', 'Reservar estoque')
@section('lead', 'Compromete saldo disponível; lote vencido bloqueado (RN-03).')
@section('breadcrumb')
    <a href="{{ route('estoque.saldo') }}">Saldo</a> / Reserva
@endsection

@section('content')
<section class="hx-card hx-card--narrow">
    <form method="post" action="{{ route('estoque.reserva.store') }}" class="hx-form">
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
            Referência (ordem, opcional)
            <input type="text" name="referencia_id" maxlength="64">
        </label>
        @error('quantidade')
            <p class="hx-field__erro">{{ $message }}</p>
        @enderror
        @error('lote_id')
            <p class="hx-field__erro">{{ $message }}</p>
        @enderror
        <button type="submit" class="hx-btn">Reservar</button>
    </form>
</section>
@endsection
