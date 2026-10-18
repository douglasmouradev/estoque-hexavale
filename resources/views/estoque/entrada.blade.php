@extends('layouts.app')

@section('title', 'Entrada · HexaVale')
@section('heading', 'Entrada de estoque')
@section('lead', 'Registra movimentação append-only e atualiza o saldo do lote.')
@section('breadcrumb')
    <a href="{{ route('estoque.saldo') }}">Saldo</a> / Entrada
@endsection

@section('content')
<section class="hx-card hx-card--narrow">
    <form method="post" action="{{ route('estoque.entrada.store') }}" class="hx-form">
        @csrf
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
            Motivo / documento
            <input type="text" name="motivo" maxlength="255">
        </label>
        @error('quantidade')
            <p class="hx-field__erro">{{ $message }}</p>
        @enderror
        @error('lote_id')
            <p class="hx-field__erro">{{ $message }}</p>
        @enderror
        <button type="submit" class="hx-btn">Registrar entrada</button>
    </form>
</section>
@endsection
