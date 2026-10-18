# ADR-0004 — Arquitetura hexagonal e Deptrac

- Status: Aceito
- Data: 2026-10-18

## Contexto

Vários contextos delimitados; regra de dependência deve ser enforced no CI.

## Decisão

Pastas `src/{Contexto}/{Dominio|Aplicacao|Infraestrutura|Interface}` + `deptrac.yaml`.
Dominio → (ninguém); Aplicacao → Dominio; Infra/Interface → para dentro.

## Consequências

+ Isolamento testável.
− Overhead de boilerplate no início.
