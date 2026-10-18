@props([
    'codigo' => 'AM-DEMO-01',
    'produto' => 'Amistar WG',
    'validade' => '2027-03-15',
    'disponivel' => '18,500 kg',
    'vencido' => false,
])
<article {{ $attributes->class(['hx-etiqueta', 'hx-etiqueta--vencido' => $vencido]) }}>
    <div class="hx-etiqueta__corte" aria-hidden="true"></div>
    <header class="hx-etiqueta__cab">
        <span class="hx-etiqueta__marca">Lote</span>
        <strong class="mono hx-etiqueta__codigo">{{ $codigo }}</strong>
    </header>
    <p class="hx-etiqueta__produto">{{ $produto }}</p>
    @if ($vencido)
        <p class="hx-chip hx-chip--er" style="margin-bottom:var(--space-3);">
            <svg class="hx-ico hx-ico--sm" aria-hidden="true"><use href="/icons.svg#i-error"/></svg>
            Vencido
        </p>
    @endif
    <dl class="hx-etiqueta__meta">
        <div><dt>Validade</dt><dd class="mono">{{ $validade }}</dd></div>
        <div><dt>Disponível</dt><dd class="mono">{{ $disponivel }}</dd></div>
    </dl>
    <div class="hx-etiqueta__qr mono">
        <svg class="hx-ico hx-ico--sm" aria-hidden="true"><use href="/icons.svg#i-lot"/></svg>
        {{ $codigo }}
    </div>
</article>
