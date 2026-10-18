# Auditoria de layout — HexaVale

**Data:** 2026-10-18  
**Escopo:** protótipo `docs/prototipo/hexavale_erp_unificado.html` + app Laravel (Fase 1).  
**Capturas “antes”:** `docs/design/capturas/antes/`

## Confirmação dos sintomas (seção 1 do brief)

| # | Sintoma | Evidência |
|---|---|---|
| 1 | Template Tailwind CDN + Inter + cards `rounded-xl`/`shadow-sm` | Protótipo: CDN Tailwind, Google Fonts Inter, cards KPI; captura `antes-prototipo-dashboard.png` |
| 2 | FontAwesome + marca com hexágono+folha | Protótipo linhas 17–18 e logo na sidebar |
| 3 | Sidebar verde-escura, pílula ativa, rótulos 10 px caixa-alta `tracking-widest` | “SUPRIMENTOS / ESTOQUE / CADASTROS”; Dashboard em pílula brand |
| 4 | Busca em pílula + sino badge “4” decorativo | Header do dashboard |
| 5 | Grade de 4 KPIs iguais + 2 gráficos sem hierarquia de decisão | Dashboard |
| 6 | Gráficos fora da paleta (roxo `#7c3aed`, amarelos, slate `#94a3b8`) | Chart.js na rosca/barra |
| 7 | Dois sistemas visuais (shell Tailwind vs módulo OT com `--bg/--ac`) | iframe OT embutido no HTML unificado |
| 8 | Selos `rounded-full` + `bg-*-100 text-*-700` | Tabelas e pedidos no protótipo |
| 9 | Microcopy genérica | “Buscar…”, badge sem destino |

## App Laravel (estado antes desta fundação)

| Item | Situação |
|---|---|
| Paleta | Já alinhada parcialmente aos tokens OT; raios ainda 8–12 px (acima do brief 2–4 px) |
| Fontes | OFL self-hosted (Barlow / Source Sans 3 / IBM Plex Mono) — OK |
| Shell | Sidebar clara tipo OT, mas item ativo em “pílula” suave |
| Assinatura | 4 componentes existem; styleguide básico |
| Capturas | `antes-entrar-desktop.png`, `antes-styleguide-desktop.png` |

## Fora de escopo nesta auditoria

Regras de negócio, rotas, permissões, cálculos — não alterados.
