@extends('layouts.app')

@section('title', 'Novo pedido · HexaVale')
@section('heading', 'Novo pedido de compra')
@section('lead', 'Rascunho com itens; envie para aprovação depois.')
@section('breadcrumb')
    <a href="{{ route('compras.index') }}">Compras</a> / Novo
@endsection

@section('content')
<section class="hx-card hx-card--narrow">
    <form method="post" action="{{ route('compras.store') }}" class="hx-form">
        @csrf
        <label class="hx-field">
            Fornecedor (opcional)
            <input type="text" name="fornecedor_nome" maxlength="255" value="{{ old('fornecedor_nome') }}">
        </label>
        <label class="hx-field">
            Observação
            <input type="text" name="observacao" maxlength="255" value="{{ old('observacao') }}">
        </label>
        <fieldset class="hx-field">
            <legend>Item</legend>
            <label class="hx-field">
                Produto
                <select name="produto_id[]" required>
                    @foreach ($produtos as $produto)
                        <option value="{{ $produto->id }}">{{ $produto->nome }} ({{ $produto->unidade_base }})</option>
                    @endforeach
                </select>
            </label>
            <label class="hx-field">
                Quantidade
                <input type="text" name="quantidade[]" class="tabular" required inputmode="decimal">
            </label>
            <label class="hx-field">
                Unidade (opcional)
                <input type="text" name="unidade[]" maxlength="8" placeholder="kg, g, L, mL">
            </label>
        </fieldset>
        @error('produto_id')
            <p class="hx-field__erro">{{ $message }}</p>
        @enderror
        <button type="submit" class="hx-btn">Salvar rascunho</button>
    </form>
</section>
@endsection
