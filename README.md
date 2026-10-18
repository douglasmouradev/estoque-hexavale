# HexaVale Estoque

Sistema de controle de estoque e operação de insumos para fazenda de manga e uva no Vale do São Francisco (Hexavale Tech Agro Desenvolvimento Ltda).

**Fase atual: 0 — Fundações** (sem funcionalidade de negócio).

## Stack

| Peça | Versão |
|---|---|
| PHP | 8.5.x |
| Laravel | 13.x (infraestrutura) |
| MySQL | 9.7.2 LTS |
| Frontend | Blade + Vite + TypeScript (Lit na Fase 1) |

## Setup (≤ 10 min)

```bash
cp .env.example .env
composer config use-github-api false   # evita timeout da API GitHub
composer install
php artisan key:generate
npm install
npm run build

# SQLite local (sem Docker)
touch database/database.sqlite
php artisan migrate   # obrigatório: cria sessions, users, cache, jobs

php artisan serve     # http://127.0.0.1:8000

# Ou stack completa
# make up             # Caddy :8080, MySQL :3307, Mailpit :8025
```

## Qualidade

```bash
make lint    # Pint
make types   # PHPStan max
make arch    # Deptrac
make test    # PHPUnit Unit
make quality # tudo acima + composer audit
```

## Rotas Fase 0

- `GET /healthz` · `GET /readyz`
- `GET /_styleguide` (somente `local`/`testing`)

## Documentação

- [Glossário](docs/glossario.md)
- [Requisitos / RN](docs/requisitos.md)
- [ADRs](docs/adr/)
- [Design](docs/design/DESIGN.md)
- [Perguntas abertas](docs/perguntas-abertas.md)
- [Protótipo HTML (referência)](docs/prototipo/hexavale_erp_unificado.html) — ver `docs/prototipo/README.md`

## Repositório

https://github.com/douglasmouradev/estoque-hexavale
