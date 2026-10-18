<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HexaVale · Styleguide (dev)</title>
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
</head>
<body>
<main class="hx-shell">
    <header style="border-bottom: var(--hx-border); padding-bottom: var(--hx-space-4); margin-bottom: var(--hx-space-6);">
        <p class="mono" style="margin:0; font-size: var(--hx-fs-xs); color: var(--hx-tinta-suave);">SOMENTE LOCAL / DEV</p>
        <h1 style="margin: var(--hx-space-2) 0 0;">HexaVale · Styleguide</h1>
        <p style="margin: var(--hx-space-2) 0 0; color: var(--hx-tinta-suave);">
            Tokens e placeholders dos componentes-assinatura. Implementação visual completa na Fase 1.
        </p>
    </header>

    <section style="margin-bottom: var(--hx-space-8);">
        <h2>Tokens de cor</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(8rem, 1fr)); gap: var(--hx-space-3);">
            @foreach ([
                'papel' => 'var(--hx-papel)',
                'tinta' => 'var(--hx-tinta)',
                'manga' => 'var(--hx-manga)',
                'uva' => 'var(--hx-uva)',
                'terra' => 'var(--hx-terra)',
                'alerta' => 'var(--hx-alerta)',
            ] as $nome => $cor)
                <div class="hx-hairline" style="padding: var(--hx-space-3);">
                    <div style="height: 2.5rem; background: {{ $cor }}; border: var(--hx-border);"></div>
                    <p class="mono" style="margin: var(--hx-space-2) 0 0; font-size: var(--hx-fs-xs);">{{ $nome }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section style="margin-bottom: var(--hx-space-8);">
        <h2>1. Etiqueta de lote</h2>
        <div class="hx-etiqueta-lote" role="img" aria-label="Placeholder Etiqueta de lote">
            <strong>EtiquetaLote</strong> — código mono, validade, QR (Fase 1)
        </div>
    </section>

    <section style="margin-bottom: var(--hx-space-8);">
        <h2>2. Régua de nível</h2>
        <div class="hx-regua-nivel" role="img" aria-label="Placeholder Régua de nível">
            <strong>ReguaNivel</strong> — saldo × mínimo × reservado (Fase 1)
        </div>
    </section>

    <section style="margin-bottom: var(--hx-space-8);">
        <h2>3. Mapa hexagonal de talhões</h2>
        <div class="hx-mapa-hex" role="img" aria-label="Placeholder Mapa hexagonal">
            <strong>MapaHexTalhao</strong> — grade por cultura e status (Fase 1)
        </div>
    </section>

    <section style="margin-bottom: var(--hx-space-8);">
        <h2>4. Ficha / carimbo</h2>
        <div class="hx-ficha-carimbo" role="img" aria-label="Placeholder Ficha carimbo">
            <strong>FichaCarimbo</strong> — documentos A/B A4 (Fase 1)
        </div>
    </section>

    <section>
        <h2>Botões</h2>
        <p>
            <button type="button" class="hx-btn">Ação primária</button>
            <button type="button" class="hx-btn hx-btn--ghost">Secundário</button>
        </p>
    </section>
</main>
</body>
</html>
