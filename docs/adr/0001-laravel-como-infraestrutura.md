# ADR-0001 — Laravel 13 como infraestrutura

- Status: Aceito
- Data: 2026-10-18

## Contexto

Precisamos de HTTP, filas, scheduler, migrations e container sem acoplar o domínio ao framework.

## Decisão

Usar **Laravel 13.37** (PHP 8.3–8.5) apenas como infraestrutura. Domínio e aplicação ficam em `src/` (namespace `HexaVale\`), sem imports de Illuminate no Dominio.

## Consequências

+ Produtividade em infra; ecossistema maduro.
− Risco de vazamento de framework no domínio — mitigado por Deptrac + testes de arquitetura.
