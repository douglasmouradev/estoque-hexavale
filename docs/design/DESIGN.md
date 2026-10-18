# DESIGN — Painel técnico de almoxarifado

## Conceito

Interface sóbria e densa: ficha, etiqueta e régua. Cor só onde há informação. Wordmark **Hexa*vale*** (peso display + *vale* itálico em `--ac`). Hexágono = estrutura (talhões), não logo com folha.

## Fontes (OFL, self-hosted)

| Papel | Família | Por quê |
|---|---|---|
| Display / colunas | Barlow Condensed 600 | Compacta em cabeçalhos de tabela e marca |
| Corpo | Source Sans 3 400/600 | Humanista legível em campo e almoxarifado |
| Dados | IBM Plex Mono 400 | Códigos, lotes, quantidades; `tabular-nums` |

ADR: `docs/adr/0005-identidade-visual-caderno.md`.

## Tokens

Arquivo: `resources/css/tokens.css` (+ `base.css`, `components.css`).

Paleta oficial (claro / escuro) idêntica ao HTML OT (`--bg` … `--erb`) + `--info` / `--info-bg` (único azul, só selos “em andamento”).

Proporção: ~80% superfície, ~15% texto/borda, ≤5% ação/estado.

### Ajustes de contraste (AA)

| Token original | Uso | Problema | Ajuste (só texto) |
|---|---|---|---|
| `--mu` `#667568` sobre `--bg` | rótulos | 4,48:1 | `--mu-ink` `#5b695d` (5,33:1) |
| `--wa` `#b26a00` sobre `--wab` | alerta | 3,86:1 | `--wa-ink` `#975a00` (5,06:1) |

Fundos/bordas permanecem nos tokens originais. Script: `php scripts/contrast.php`.

### Reserva

- Brand 800–950 só em ênfase / cabeçalho de ficha impressa.
- Proibido: `#22c55e`, `#4ade80`, roxo, rosa, ciano, cinza-ardósia de gráfico.

Verificação: `php scripts/check-palette.php`.

## Forma

- Grade 8 px; raio **2–4 px**; tabelas raio 0.
- Separação por borda 1 px `--bd`; sombra só em toast/diálogo.
- Sem gradiente, glass, blur, sombra colorida, `rounded-full` em busca/selo.

## Componentes

Navegação (barra 3 px + `--ac2`), chips (quadrado + glifo + texto), alertas `.al`, abas, formulário com sufixo de unidade, tabela sticky, toast, diálogo, **EtiquetaLote**, **ReguaNivel**, **MapaHexTalhao**, **FichaCarimbo**.

Ícones: `public/icons.svg` (traço 1,5 px, 20×20).

## Styleguide

`/_styleguide` (local/testing) — claro/escuro via `data-theme`.

## Módulo OT (iframe)

**Etapa 4 (pendente de aprovação):** unificar tokens/nav ao shell Laravel; retirar iframe visual. Até lá, o guia de estilo é a fonte da verdade unificada.

## Capturas

- Antes: `docs/design/capturas/antes/`
- Depois (guia): `docs/design/capturas/depois/`
