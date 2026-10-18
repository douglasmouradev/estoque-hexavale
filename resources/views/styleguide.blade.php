<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>HexaVale · Styleguide</title>
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
</head>
<body>
<div class="hx-app">
    <aside class="hx-side">
        <a href="{{ route('styleguide') }}" class="hx-logo">Hexa<em>vale</em></a>
        <p class="hx-side__sub">Guia de estilo · dev</p>
        <nav class="hx-side__nav" aria-label="Seções">
            <a href="#paleta" class="on">Paleta</a>
            <a href="#tipografia">Tipografia</a>
            <a href="#nav">Navegação</a>
            <a href="#chips">Status</a>
            <a href="#form">Formulário</a>
            <a href="#tabela">Tabela</a>
            <a href="#assinatura">Assinatura</a>
        </nav>
        <p class="hx-side__nota">Somente local/testing. Sem CDN.</p>
    </aside>

    <main class="hx-main">
        <header class="hx-page__cab">
            <div>
                <p class="hx-breadcrumb">Dev / Styleguide</p>
                <h1>Guia de estilo</h1>
                <p class="hx-page__lead">Fundação visual — Etapas 1–3. Aguardando aprovação antes de aplicar nas telas de negócio.</p>
            </div>
            <div class="hx-theme-bar noprint" role="group" aria-label="Tema">
                <button type="button" class="hx-btn hx-btn--sm hx-btn--ghost" data-theme-set="light">
                    <svg class="hx-ico hx-ico--sm" aria-hidden="true"><use href="/icons.svg#i-sun"/></svg>
                    Claro
                </button>
                <button type="button" class="hx-btn hx-btn--sm hx-btn--ghost" data-theme-set="dark">
                    <svg class="hx-ico hx-ico--sm" aria-hidden="true"><use href="/icons.svg#i-moon"/></svg>
                    Noite
                </button>
            </div>
        </header>

        <section id="paleta" class="hx-card" style="margin-bottom:var(--space-6);">
            <h2>Paleta oficial</h2>
            <div class="hx-swatch">
                @foreach ([
                    ['--bg', 'var(--bg)'], ['--card', 'var(--card)'], ['--tx', 'var(--tx)'], ['--mu', 'var(--mu)'],
                    ['--bd', 'var(--bd)'], ['--ac', 'var(--ac)'], ['--ac2', 'var(--ac2)'], ['--wa', 'var(--wa)'],
                    ['--wab', 'var(--wab)'], ['--er', 'var(--er)'], ['--erb', 'var(--erb)'], ['--info', 'var(--info)'],
                    ['--info-bg', 'var(--info-bg)'], ['--mu-ink', 'var(--mu-ink)'], ['--wa-ink', 'var(--wa-ink)'],
                ] as [$name, $color])
                    <div class="hx-swatch__item">
                        <div class="hx-swatch__chip" style="background:{{ $color }}"></div>
                        <div class="hx-swatch__meta">{{ $name }}</div>
                    </div>
                @endforeach
            </div>
        </section>

        <section id="tipografia" class="hx-card" style="margin-bottom:var(--space-6);">
            <h2>Tipografia</h2>
            <p style="font-family:var(--font-display);font-size:var(--fs-7);margin:0 0 var(--space-2);">Barlow Condensed — cabeçalhos</p>
            <p style="font-family:var(--font-body);margin:0 0 var(--space-2);">Source Sans 3 — leitura contínua em operação de campo e almoxarifado.</p>
            <p class="mono" style="margin:0;">IBM Plex Mono — AM-0107 · 18,500 kg · R$ 1.234,56</p>
        </section>

        <section id="nav" class="hx-card" style="margin-bottom:var(--space-6);">
            <h2>Navegação ativa</h2>
            <p class="hx-muted">Barra 3 px à esquerda + fundo --ac2. Sem pílula.</p>
            <div class="hx-tabs" role="tablist">
                <button type="button" class="on" role="tab" aria-selected="true">Operação real</button>
                <button type="button" role="tab" aria-selected="false">Informações técnicas</button>
                <button type="button" role="tab" aria-selected="false">Documento A</button>
                <button type="button" role="tab" aria-selected="false">Documento B</button>
                <button type="button" role="tab" aria-selected="false">Histórico</button>
            </div>
        </section>

        <section id="chips" class="hx-card" style="margin-bottom:var(--space-6);">
            <h2>Chips de status</h2>
            <div class="hx-row">
                <span class="hx-chip hx-chip--ok"><svg class="hx-ico hx-ico--sm" aria-hidden="true"><use href="/icons.svg#i-check"/></svg> Concluída</span>
                <span class="hx-chip hx-chip--wa"><svg class="hx-ico hx-ico--sm" aria-hidden="true"><use href="/icons.svg#i-warn"/></svg> Aguardando aprovação</span>
                <span class="hx-chip hx-chip--er"><svg class="hx-ico hx-ico--sm" aria-hidden="true"><use href="/icons.svg#i-error"/></svg> Abaixo do mínimo</span>
                <span class="hx-chip hx-chip--info"><svg class="hx-ico hx-ico--sm" aria-hidden="true"><use href="/icons.svg#i-info"/></svg> Em execução</span>
            </div>
            <div class="hx-alert hx-alert--wa" style="margin-top:var(--space-3);">
                <svg class="hx-ico hx-ico--sm" aria-hidden="true"><use href="/icons.svg#i-warn"/></svg>
                Saldo insuficiente no lote AM-0107: faltam 3,2 kg.
            </div>
            <div class="hx-alert hx-alert--er">
                <svg class="hx-ico hx-ico--sm" aria-hidden="true"><use href="/icons.svg#i-error"/></svg>
                Lote AM-0098 vencido em 2025-11-02.
            </div>
            <div class="hx-alert hx-alert--ok">
                <svg class="hx-ico hx-ico--sm" aria-hidden="true"><use href="/icons.svg#i-check"/></svg>
                Reserva confirmada para a ordem OT-0042.
            </div>
        </section>

        <section id="form" class="hx-card hx-card--narrow" style="margin-bottom:var(--space-6);">
            <h2>Formulário</h2>
            <form class="hx-form" onsubmit="return false;">
                <label class="hx-field">
                    Produto
                    <input type="text" value="Amistar WG" readonly>
                </label>
                <label class="hx-field">
                    Quantidade
                    <span class="hx-field__ctrl">
                        <input type="text" class="tabular" value="12,500" inputmode="decimal">
                        <span class="hx-field__sufixo">kg</span>
                    </span>
                </label>
                <p class="hx-field__erro">Informe a quantidade planejada em kg.</p>
                <button type="button" class="hx-btn">Registrar entrada</button>
            </form>
        </section>

        <section id="tabela" class="hx-card" style="margin-bottom:var(--space-6);">
            <h2>Tabela</h2>
            <div class="hx-table-wrap">
                <table class="hx-table">
                    <thead>
                        <tr>
                            <th>Lote</th>
                            <th>Produto</th>
                            <th>Saldo</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="mono">AM-0107</td>
                            <td>Amistar WG</td>
                            <td class="num">18,500 kg</td>
                            <td><span class="hx-chip hx-chip--ok"><svg class="hx-ico hx-ico--sm" aria-hidden="true"><use href="/icons.svg#i-check"/></svg> OK</span></td>
                        </tr>
                        <tr>
                            <td class="mono">UR-221</td>
                            <td>Uréia 45%</td>
                            <td class="num">2,100 kg</td>
                            <td><span class="hx-chip hx-chip--er"><svg class="hx-ico hx-ico--sm" aria-hidden="true"><use href="/icons.svg#i-error"/></svg> Abaixo do mínimo</span></td>
                        </tr>
                        <tr>
                            <td class="mono">OT-0042</td>
                            <td>Ordem N03</td>
                            <td class="num">—</td>
                            <td><span class="hx-chip hx-chip--info"><svg class="hx-ico hx-ico--sm" aria-hidden="true"><use href="/icons.svg#i-info"/></svg> Em execução</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section id="assinatura" class="hx-card" style="margin-bottom:var(--space-6);">
            <h2>1. Etiqueta de lote</h2>
            <div class="hx-row" style="align-items:stretch;">
                <x-etiqueta-lote />
                <x-etiqueta-lote codigo="AM-0098" validade="2025-11-02" :vencido="true" disponivel="0 kg" />
            </div>
        </section>

        <section class="hx-card" style="margin-bottom:var(--space-6);">
            <h2>2. Régua de nível</h2>
            <x-regua-nivel :fisico="100" :reservado="35" :minimo="40" unidade="kg" />
        </section>

        <section class="hx-card" style="margin-bottom:var(--space-6);">
            <h2>3. Mapa hexagonal de talhões</h2>
            <p class="hx-muted">Manga = fundo âmbar; uva = fundo ac2 + hachura. Status por anel + rótulo.</p>
            <x-mapa-hex-talhao />
        </section>

        <section class="hx-card">
            <h2>4. Ficha / carimbo</h2>
            <x-ficha-carimbo numero="000125" :confirmada="false">
                <p>Talhão N 03 · Manga Kent · 8,3 ha · calda 800 L/ha</p>
            </x-ficha-carimbo>
        </section>
    </main>
</div>
<script>
(() => {
  const root = document.documentElement;
  document.querySelectorAll('[data-theme-set]').forEach((btn) => {
    btn.addEventListener('click', () => {
      root.setAttribute('data-theme', btn.getAttribute('data-theme-set'));
    });
  });
})();
</script>
</body>
</html>
