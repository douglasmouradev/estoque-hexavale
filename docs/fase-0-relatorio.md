# Relatório — Fase 0 (Fundações)

**Data:** 2026-10-18  
**Repositório:** https://github.com/douglasmouradev/estoque-hexavale  
**Portão:** aguardando aprovação humana para iniciar a Fase 1.

## O que foi feito

- Scaffold **Laravel 13.37** + PHP **8.5.11**
- Protótipo HTML movido para `docs/prototipo/hexavale_erp_unificado.html`
- Estrutura hexagonal em `src/` (11 contextos × 4 camadas)
- Docker Compose: php-fpm 8.5, Caddy, **MySQL 9.7.2**, Mailpit, worker
- Makefile: `up`, `lint`, `types`, `arch`, `test`, `sec`, `e2e`, `quality`
- PHPStan level max, Pint, Deptrac, CI GitHub Actions
- Docs: glossário, requisitos RN-01…20, ADRs 0001–0006, C4, LGPD/RBAC stubs, perguntas, backlog
- Design tokens + `/_styleguide` (dev) + Vite/TS **sem Tailwind/CDN**
- `.cursor/rules/*.mdc` (7 regras permanentes)
- Rotas `/healthz`, `/readyz`, `/_styleguide`

## Versões fixadas

| Componente | Versão |
|---|---|
| PHP | 8.5.x (local 8.5.11) |
| Laravel | 13.37.0 |
| MySQL | 9.7.2 (`mysql:9.7.2`) |
| Vite | 8.x |
| PHPStan / Deptrac / Pint | via composer.lock |

## Assumido (perguntas abertas)

Q-01 multi-fazenda; Q-02 hosting BR; Q-03 LLM portas abstratas; Q-04 NF-e futura; Q-06 bulas upload RT; Q-07 retenção 5 anos. **Q-05 DPO/porte ANPD permanece aberto.**

## Suíte (local)

- `make lint` — OK  
- `make types` — OK (0 erros)  
- `make arch` — OK (0 violações)  
- `make test` — OK (3 testes)  
- `composer audit` — sem advisories (cache local)

## Riscos / pendências

| ID | Risco / pendência |
|---|---|
| BK-FONT-01 | Fontes OFL ainda system-fallback |
| BK-LIT-01 | Lit ainda não introduzido |
| BK-SEC-01 | gitleaks/Trivy/npm audit completos na Fase 1 |
| BK-E2E-01 | Playwright/Lighthouse skip |
| — | Imagem Docker PHP ainda não pinada por digest |
| — | `composer` com `use-github-api false` por timeouts da API GitHub no ambiente |

## Fora do escopo (próximas fases)

Identidade, estoque ledger, compras, ordens, PWA, RAG, portal LGPD.

---

**Pare aqui.** Aprovar explicitamente a Fase 0 para liberar a Fase 1 (Identidade + auditoria + privacidade base + componentes-assinatura).
