@props([
    'titulo' => 'Ordem de pulverização foliar',
    'numero' => '000125',
    'versao' => '1',
    'confirmada' => false,
])
<article {{ $attributes->merge(['class' => 'hx-ficha']) }}>
    <header class="hx-ficha__cab">
        <div>
            <p class="hx-ficha__marca">Hexa<em>vale</em></p>
            <small>Hexavale Tech Agro · documento de campo</small>
        </div>
        <div class="hx-ficha__centro">
            <strong>{{ $titulo }}</strong>
            @unless ($confirmada)
                <span class="hx-ficha__selo">Execução não confirmada</span>
            @endunless
        </div>
        <div class="hx-ficha__dir mono">
            <div>Nº {{ $numero }}</div>
            <small>Versão {{ $versao }}</small>
        </div>
    </header>
    <div class="hx-ficha__corpo">
        {{ $slot }}
    </div>
    <footer class="hx-ficha__assinaturas">
        <div>Responsável técnico</div>
        <div>Supervisor</div>
        <div>Aplicador</div>
    </footer>
</article>
