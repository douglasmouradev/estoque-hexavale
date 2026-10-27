@extends('layouts.app')

@section('title', 'Nova ordem · HexaVale')
@section('heading', 'Nova ordem de campo')
@section('lead', 'Dose × área (RN-05). Salvar não movimenta estoque (RN-01).')
@section('breadcrumb')
    <a href="{{ route('operacao.index') }}">Ordens</a> / Nova
@endsection

@section('content')
<section class="hx-card hx-card--narrow">
    <form method="post" action="{{ route('operacao.store') }}" class="hx-form">
        @csrf
        <label class="hx-field">
            Tipo
            <select name="tipo" required>
                <option value="adubacao">Adubação</option>
                <option value="pulverizacao">Pulverização</option>
            </select>
        </label>
        <label class="hx-field">
            Talhão
            <select name="talhao_id" required>
                @foreach ($talhoes as $t)
                    <option value="{{ $t->id }}">{{ $t->codigo }} ({{ $t->area_ha }} ha)</option>
                @endforeach
            </select>
        </label>
        <label class="hx-field">
            Área planejada (ha)
            <input type="text" name="area_planejada_ha" class="tabular" required inputmode="decimal">
        </label>
        <label class="hx-field">
            Calda L/ha (opcional; pulverização por 100 L)
            <input type="text" name="calda_l_ha" class="tabular" inputmode="decimal">
        </label>
        <label class="hx-field">
            Lote
            <select name="lote_id" required>
                @foreach ($lotes as $lote)
                    <option value="{{ $lote->id }}">{{ $lote->produto?->nome }} · {{ $lote->codigo }} ({{ $lote->produto?->unidade_base }})</option>
                @endforeach
            </select>
        </label>
        <label class="hx-field">
            Dose (por ha, ou por 100 L se informar calda)
            <input type="text" name="dose" class="tabular" required inputmode="decimal">
        </label>
        <label class="hx-field">
            Observação
            <input type="text" name="observacao" maxlength="255">
        </label>
        @error('dose')
            <p class="hx-field__erro">{{ $message }}</p>
        @enderror
        @error('lote_id')
            <p class="hx-field__erro">{{ $message }}</p>
        @enderror
        <button type="submit" class="hx-btn">Salvar rascunho</button>
    </form>
</section>
@endsection
