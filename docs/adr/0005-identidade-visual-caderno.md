# ADR-0005 — Identidade visual “painel técnico de almoxarifado”

- Status: Aceito (atualizado 2026-10-18)
- Data: 2026-10-18

## Contexto

O protótipo HTML misturava Tailwind/Inter/FontAwesome com um módulo de Ordens de Trabalho que já tinha a paleta correta (`--bg`, `--ac`…). Era preciso uma identidade única, sem CDN, alinhada ao domínio (fazenda manga/uva, almoxarifado).

## Decisão

1. **Paleta inegociável** do HTML OT (claro + escuro + `--info` semântico).
2. **Tipografia OFL self-hosted:** Barlow Condensed (display), Source Sans 3 (corpo), IBM Plex Mono (dados).
3. **Forma:** raio 2–4 px, borda 1 px, sem sombra colorida; wordmark Hexa*vale* sem folha.
4. Quatro componentes-assinatura + sprite `public/icons.svg`.
5. Contraste AA com tokens de texto `*-ink` quando o par oficial falha (matiz preservado).

## Consequências

+ Um sistema visual; styleguide em `/_styleguide`.
− Unificação do iframe OT fica para a Etapa 4 (após portão de aprovação).
− Roxo/Tailwind green removidos da UI do app; protótipo HTML ainda é referência histórica até a Etapa 4.
