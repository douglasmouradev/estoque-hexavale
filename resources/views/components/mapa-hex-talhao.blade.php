@props([
    'talhoes' => [
        ['id' => 'N03', 'cultura' => 'manga', 'status' => 'ok'],
        ['id' => 'N04', 'cultura' => 'manga', 'status' => 'alerta'],
        ['id' => 'S01', 'cultura' => 'uva', 'status' => 'ok'],
        ['id' => 'S02', 'cultura' => 'uva', 'status' => 'execucao'],
        ['id' => 'N05', 'cultura' => 'manga', 'status' => 'ok'],
        ['id' => 'S03', 'cultura' => 'uva', 'status' => 'ok'],
    ],
])
<div {{ $attributes->merge(['class' => 'hx-mapa-hex']) }} role="list">
    @foreach ($talhoes as $t)
        <button type="button" class="hx-hex hx-hex--{{ $t['cultura'] }} hx-hex--{{ $t['status'] }}" role="listitem" title="Talhão {{ $t['id'] }}">
            <span class="mono">{{ $t['id'] }}</span>
            <small>{{ $t['cultura'] }}</small>
        </button>
    @endforeach
</div>
