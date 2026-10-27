@extends('layouts.app')

@section('title', 'Painel · HexaVale')
@section('heading', 'Pendências do dia')
@section('lead', 'O que bloqueia operação agora — estoque real da fazenda.')
@section('breadcrumb')
    Hexa<em>vale</em> / Painel
@endsection
@section('acoes')
    <a href="{{ route('estoque.entrada') }}" class="hx-btn hx-btn--sm">Entrada</a>
    <a href="{{ route('estoque.ajuste') }}" class="hx-btn hx-btn--sm hx-btn--ghost">Ajuste</a>
    <a href="{{ route('estoque.reserva') }}" class="hx-btn hx-btn--sm hx-btn--ghost">Reserva</a>
@endsection

@section('content')
@if (count($pendencias) === 0)
    <p class="hx-alert hx-alert--ok" role="status">Nenhuma pendência crítica de estoque.</p>
@else
    <section class="hx-card" style="margin-bottom:var(--space-6);">
        <h2>Prioridade</h2>
        <ul style="margin:0;padding-left:1.1rem;">
            @foreach ($pendencias as $p)
                <li style="margin-bottom:var(--space-2);">
                    @if ($p['tipo'] === 'vencido')
                        <span class="hx-chip hx-chip--er">Vencido</span>
                    @elseif ($p['tipo'] === 'zerado')
                        <span class="hx-chip hx-chip--er">Zerado</span>
                    @else
                        <span class="hx-chip hx-chip--wa">Abaixo</span>
                    @endif
                    {{ $p['texto'] }}
                    — <a href="{{ $p['href'] }}">Resolver</a>
                </li>
            @endforeach
        </ul>
    </section>
@endif

<section class="hx-card" style="margin-bottom:var(--space-6);">
    <h2>Régua — críticos</h2>
    <div class="hx-grid hx-grid--2">
        @php
            $criticos = array_values(array_filter($saldos, fn ($s) => in_array($s['situacao'], ['abaixo', 'zerado'], true) || $s['vencido']));
        @endphp
        @forelse (array_slice($criticos, 0, 4) as $s)
            <x-regua-nivel
                :fisico="(float) $s['fisico']"
                :reservado="(float) $s['reservado']"
                :minimo="(float) $s['minimo']"
                :unidade="$s['unidade']"
            />
            <p class="hx-muted mono">{{ $s['produto'] }} · {{ $s['lote'] }}</p>
        @empty
            <p class="hx-muted">Sem itens críticos. Veja o <a href="{{ route('estoque.saldo') }}">saldo completo</a>.</p>
        @endforelse
    </div>
</section>

<section class="hx-card">
    <h2>Saldo resumido</h2>
    <div class="hx-table-wrap">
        <table class="hx-table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Lote</th>
                    <th>Disponível</th>
                    <th>Situação</th>
                </tr>
            </thead>
            <tbody>
                @foreach (array_slice($saldos, 0, 8) as $s)
                    <tr>
                        <td>{{ $s['produto'] }}</td>
                        <td class="mono">{{ $s['lote'] }}</td>
                        <td class="num">{{ $s['disponivel'] }} {{ $s['unidade'] }}</td>
                        <td>
                            @if ($s['vencido'])
                                <span class="hx-chip hx-chip--er">Vencido</span>
                            @elseif ($s['situacao'] === 'ok')
                                <span class="hx-chip hx-chip--ok">OK</span>
                            @elseif ($s['situacao'] === 'abaixo')
                                <span class="hx-chip hx-chip--wa">Abaixo</span>
                            @else
                                <span class="hx-chip hx-chip--er">Zerado</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <p style="margin-top:var(--space-3);"><a href="{{ route('estoque.saldo') }}">Ver saldo completo</a></p>
</section>
@endsection
