@extends('layouts.app')

@section('title', 'Saldo · HexaVale')
@section('heading', 'Saldo de estoque')
@section('lead', 'Disponível = físico − reservado (RN-02). Situação Zerado / Abaixo / OK (RN-14).')
@section('breadcrumb')
    <a href="{{ route('painel') }}">Painel</a> / Estoque
@endsection
@section('acoes')
    <a href="{{ route('estoque.entrada') }}" class="hx-btn hx-btn--sm">Entrada</a>
    <a href="{{ route('estoque.saida') }}" class="hx-btn hx-btn--sm hx-btn--ghost">Saída</a>
    <a href="{{ route('estoque.ajuste') }}" class="hx-btn hx-btn--sm hx-btn--ghost">Ajuste</a>
    <a href="{{ route('estoque.reserva') }}" class="hx-btn hx-btn--sm hx-btn--ghost">Reserva</a>
    <a href="{{ route('estoque.reserva.liberar') }}" class="hx-btn hx-btn--sm hx-btn--ghost">Liberar</a>
@endsection

@section('content')
<section class="hx-card">
    <div class="hx-table-wrap">
        <table class="hx-table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Lote</th>
                    <th>Físico</th>
                    <th>Reservado</th>
                    <th>Disponível</th>
                    <th>Mínimo</th>
                    <th>Situação</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($saldos as $s)
                    <tr>
                        <td>{{ $s['produto'] }}</td>
                        <td class="mono">{{ $s['lote'] }}</td>
                        <td class="num">{{ $s['fisico'] }} {{ $s['unidade'] }}</td>
                        <td class="num">{{ $s['reservado'] }}</td>
                        <td class="num">{{ $s['disponivel'] }}</td>
                        <td class="num">{{ $s['minimo'] }}</td>
                        <td>
                            @if ($s['vencido'] ?? false)
                                <span class="hx-chip hx-chip--er">Vencido</span>
                            @elseif ($s['situacao'] === 'ok')
                                <span class="hx-chip hx-chip--ok">OK</span>
                            @elseif ($s['situacao'] === 'abaixo')
                                <span class="hx-chip hx-chip--wa">Abaixo do mínimo</span>
                            @else
                                <span class="hx-chip hx-chip--er">Zerado</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">Nenhum lote. Rode o seed da Fase 2.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
