@extends('layouts.app')

@section('title', 'Compras · HexaVale')
@section('heading', 'Pedidos de compra')
@section('lead', 'Solicitação, aprovação segregada (RN-12) e recebimento com chave NF-e.')
@section('breadcrumb')
    <a href="{{ route('painel') }}">Painel</a> / Compras
@endsection
@section('acoes')
    <a href="{{ route('compras.create') }}" class="hx-btn hx-btn--sm">Novo pedido</a>
@endsection

@section('content')
<section class="hx-card">
    <div class="hx-table-wrap">
        <table class="hx-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fornecedor</th>
                    <th>Status</th>
                    <th>Itens</th>
                    <th>Criado</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pedidos as $p)
                    <tr>
                        <td><a href="{{ route('compras.show', $p['id']) }}" class="mono">{{ \Illuminate\Support\Str::limit($p['id'], 8, '') }}</a></td>
                        <td>{{ $p['fornecedor_nome'] ?? '—' }}</td>
                        <td><span class="hx-chip">{{ $p['status'] }}</span></td>
                        <td class="num">{{ $p['itens'] }}</td>
                        <td>{{ $p['criado_em'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">Nenhum pedido. Crie o primeiro.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
