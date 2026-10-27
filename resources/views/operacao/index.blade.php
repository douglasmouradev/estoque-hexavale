@extends('layouts.app')

@section('title', 'Ordens · HexaVale')
@section('heading', 'Ordens agrícolas')
@section('lead', 'Rascunho não movimenta; liberar reserva; baixa consome estoque (RN-01).')
@section('breadcrumb')
    <a href="{{ route('painel') }}">Painel</a> / Operação
@endsection
@section('acoes')
    <a href="{{ route('operacao.create') }}" class="hx-btn hx-btn--sm">Nova ordem</a>
@endsection

@section('content')
<section class="hx-card">
    <div class="hx-table-wrap">
        <table class="hx-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipo</th>
                    <th>Talhão</th>
                    <th>Área (ha)</th>
                    <th>Status</th>
                    <th>Criado</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ordens as $o)
                    <tr>
                        <td><a href="{{ route('operacao.show', $o['id']) }}" class="mono">{{ \Illuminate\Support\Str::limit($o['id'], 8, '') }}</a></td>
                        <td>{{ $o['tipo'] }}</td>
                        <td>{{ $o['talhao'] }}</td>
                        <td class="num">{{ $o['area_planejada_ha'] }}</td>
                        <td><span class="hx-chip">{{ $o['status'] }}</span></td>
                        <td>{{ $o['criado_em'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6">Nenhuma ordem.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
