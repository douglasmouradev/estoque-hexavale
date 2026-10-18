# ADR-0003 — Frontend Blade + Vite/TS + Lit

- Status: Aceito
- Data: 2026-10-18

## Contexto

Protótipo usava Vue/Tailwind/Chart via CDN (D11). Offline e privacidade exigem self-host.

## Decisão

- SSR com **Blade**
- Assets com **Vite + TypeScript estrito**
- Ilhas com **Lit** a partir da Fase 1
- **Zero CDN** em runtime; CSS próprio com tokens
- Sem Tailwind, Bootstrap, Material, shadcn, FontAwesome, Chart.js default

## Consequências

+ Controle total de design e CSP.
− Mais trabalho de UI; Lit ainda não na Fase 0 (BK-LIT-01).
