# DESIGN — Caderno de campo + etiqueta de almoxarifado

## Conceito

Sistema utilitário, denso e técnico: papel, tinta, carimbo, régua. O hexágono é **estrutura** (talhões, indicadores), não logo decorativo.

## Proibições

Gradientes (roxo/azul), glassmorphism, sombras difusas, cards 16px iguais, emojis, ilustrações genéricas de IA, hero centralizado, microcopy “Olá!”, Inter/Roboto/Poppins/Montserrat, verde Tailwind, FontAwesome genérico, animações sem função, CDN de CSS/JS/fontes.

## Tokens

Ver `resources/css/tokens.css`:

- Superfície: papel `#F3EFE6`, tinta `#1D1B16`, linhas `#CFC7B4`
- Manga = âmbar; uva = bordô; terra = ação; laranja = alerta
- Estados sempre: cor + texto + forma

## Componentes-assinatura (Fase 1)

1. EtiquetaLote
2. ReguaNivel
3. MapaHexTalhao
4. FichaCarimbo

Styleguide: `/_styleguide` (somente `local`/`testing`).

## Tipografia (ADR-0005)

Condensada + humanista + mono tabular; OFL; self-hosted WOFF2 na Fase 1.
