# ADR-0009 — Ledger de estoque (Fase 2)

- Status: Aceito
- Data: 2026-10-18

## Contexto

Precisamos de razão imutável por fazenda/lote, precisão decimal e projeção de saldo sem float.

## Decisão

- Tabela `movimentacoes` **append-only** (trigger bloqueia UPDATE/DELETE).
- Quantidades como `DECIMAL(24,6)` + `brick/math` no domínio (`Quantidade`).
- Tipos: `entrada`, `saida`, `ajuste_pos`, `ajuste_neg`, `reserva`, `baixa_reserva`.
- `saldos_lote` é projeção atualizada na mesma transação (físico / reservado).
- `disponivel = fisico − reservado` (RN-02); saída não deixa físico negativo (RN-07).
- Cadastros referenciados usam soft-delete / bloqueio (RN-13).
- Idempotency-Key em POST mutável (coluna + unique por fazenda).

## Consequências

+ Auditoria de estoque reconstruível; multi-fazenda nativo.
− Reservas de ordem agrícola completa ficam na Fase 5; UI `/app` protótipo ainda convivendo.
