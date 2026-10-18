# ADR-0002 — MySQL 9.7.2 LTS e ledger append-only

- Status: Aceito
- Data: 2026-10-18

## Contexto

Estoque exige razão imutável, multi-fazenda e precisão decimal. MySQL 8.0 está em EOL.

## Decisão

- Imagem `mysql:9.7.2` (LTS)
- `utf8mb4` / `utf8mb4_0900_ai_ci`, `sql_mode` estrito
- Futuro: `movimentacoes` append-only; `saldos_lote` como projeção

## Consequências

+ Linha LTS suportada; DECIMAL nativo.
− Vetores MySQL variam por edição — porta `RepositorioVetorial` com fallback (Fase 9).
