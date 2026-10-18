@props([
    'fisico' => 100,
    'reservado' => 20,
    'minimo' => 30,
    'unidade' => 'kg',
])
@php
    $fisico = max(0, (float) $fisico);
    $reservado = max(0, min((float) $reservado, $fisico));
    $minimo = max(0, (float) $minimo);
    $topo = max($fisico, $minimo, 1);
    $pctF = 100 * $fisico / $topo;
    $pctR = 100 * $reservado / $topo;
    $pctM = 100 * $minimo / $topo;
    $disponivel = $fisico - $reservado;
@endphp
<figure {{ $attributes->merge(['class' => 'hx-regua']) }}>
    <figcaption class="hx-regua__legenda">
        <span>Físico <strong class="mono">{{ number_format($fisico, 3, ',', '.') }} {{ $unidade }}</strong></span>
        <span>Reservado <strong class="mono">{{ number_format($reservado, 3, ',', '.') }}</strong></span>
        <span>Disponível <strong class="mono">{{ number_format($disponivel, 3, ',', '.') }}</strong></span>
        <span>Mínimo <strong class="mono">{{ number_format($minimo, 3, ',', '.') }}</strong></span>
    </figcaption>
    <div class="hx-regua__trilho" role="img" aria-label="Régua de nível de estoque">
        <div class="hx-regua__fisico" style="height: {{ $pctF }}%"></div>
        <div class="hx-regua__reservado" style="height: {{ $pctR }}%"></div>
        <div class="hx-regua__minimo" style="bottom: {{ $pctM }}%"></div>
        <ol class="hx-regua__marcas" aria-hidden="true">
            <li></li><li></li><li></li><li></li><li></li>
        </ol>
    </div>
</figure>
