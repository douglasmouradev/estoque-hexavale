# ADR-0012 — Ordens agrícolas (Fase 5)

- Status: Aceito
- Data: 2026-10-27

## Contexto

Compras e estoque avançado existem. Faltava o ciclo de ordem de campo com reserva e baixa.

## Decisão

- Status: `rascunho → liberada → baixada|cancelada`.
- RN-01: rascunho não movimenta; liberar chama `ReservarEstoque`; cancelar exige motivo e devolve reserva.
- RN-05: `FormulaDose` (dose×área; pulverização opcional por 100 L de calda).
- RN-06: `PoliticaAreaTratada` — tratada ∈ (0, planejada].
- RN-08: baixa só a partir de `liberada` (uma vez).
- Consumo: `ConsumirReserva` = `baixa_reserva` + `saida`; sobra de área parcial volta via `LiberarReserva`.
- RN-10/11 (correção/bula) ficam para evolução.

## Consequências

+ Operação de campo integrada ao ledger.
− Sem versão de correção nem conferência de bula nesta entrega.
