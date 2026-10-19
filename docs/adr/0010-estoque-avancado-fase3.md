# ADR-0010 — Estoque avançado (Fase 3)

- Status: Aceito
- Data: 2026-10-19

## Contexto

Fase 2 entregou ledger básico. Faltavam vencimento, conversão, ajuste e UI sem CDN.

## Decisão

- RN-03 via `PoliticaLote` em saída/reserva.
- RN-04 via `ConversorUnidade` (massa/volume).
- RN-09 via `RegistrarAjusteInventario` (motivo + contagem ≥ reservado).
- RN-18 via middleware `RequireIdempotencyKey` + campo `_idempotency_key` nos forms.
- Reservas no ledger (`reserva` / `baixa_reserva`).
- `/app` permanece o protótipo HTML (pedido do produto); unificação sem CDN fica em BK-UI-APP-01.
- Blade `painel/show` fica como referência futura do shell.

## Consequências

+ Operação de almoxarifado utilizável sem CDN.
− Ordens agrícolas e consumo de reserva na baixa ficam na Fase 5.
