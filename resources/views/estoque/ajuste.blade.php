@extends('layouts.app')

@section('title', 'Ajuste · HexaVale')
@section('heading', 'Ajuste de inventário')
@section('lead', 'Contagem física ≥ reservado; motivo obrigatório (RN-09).')
@section('breadcrumb')
    <a href="{{ route('estoque.saldo') }}">Saldo</a> / Ajuste
@endsection

@section('content')
<section class="hx-card hx-card--narrow">
    <form method="post" action="{{ route('estoque.ajuste.store') }}" class="hx-form">
        @csrf
        <input type="hidden" name="_idempotency_key" value="{{ $idempotencyKey }}">
        <label class="hx-field">
            Lote
            <select name="lote_id" required>
                @foreach ($lotes as $lote)
                    <option value="{{ $lote->id }}">{{ $lote->produto?->nome }} · {{ $lote->codigo }}</option>
                @endforeach
            </select>
        </label>
        <label class="hx-field">
            Contagem física
            <input type="text" name="contagem" class="tabular" required inputmode="decimal">
        </label>
        <label class="hx-field">
            Unidade (opcional; converte para a base do produto)
            <input type="text" name="unidade" placeholder="kg, g, L, mL" maxlength="8">
        </label>
        <label class="hx-field">
            Motivo
            <input type="text" name="motivo" required maxlength="255">
        </label>
        @error('contagem')
            <p class="hx-field__erro">{{ $message }}</p>
        @enderror
        @error('motivo')
            <p class="hx-field__erro">{{ $message }}</p>
        @enderror
        <button type="submit" class="hx-btn">Registrar ajuste</button>
    </form>
</section>
@endsection
