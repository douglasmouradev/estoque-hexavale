# Relatório — Fase 3 (Estoque avançado + shell sem CDN)

**Data:** 2026-10-19  
**Repositório:** https://github.com/douglasmouradev/estoque-hexavale  
**Portão:** aguardando aprovação humana para iniciar a Fase 4.

## O que foi feito

- **ADR-0010** — estoque avançado: vencimento, conversão, ajuste, reservas, Idempotency-Key
- **RN-03** — `PoliticaLote` bloqueia saída/reserva de lote vencido
- **RN-04** — `ConversorUnidade` (kg/g, L/mL) na entrada/saída/ajuste
- **RN-09** — `RegistrarAjusteInventario` (motivo + contagem ≥ reservado) + UI `/estoque/ajuste`
- **Reservas** — `ReservarEstoque` / `LiberarReserva` no ledger (`reserva` / `baixa_reserva`)
- **RN-18** — middleware `RequireIdempotencyKey` nos POSTs de estoque + `_idempotency_key` nos forms
- **BK-UI-APP-01** — tentativa de painel Blade; **revertido a pedido**: `/app` volta a servir o HTML do protótipo (`resources/prototipo/…`) exatamente como deixado

## Suíte (`make quality`)

- lint / types / arch / **31 testes** / composer audit — OK

## Fora / próxima fase (4)

- Compras / NF-e / entrada fiscal
- Ordens agrícolas e consumo de reserva na baixa (Fase 5)
- Passkeys UI (BK-PASSKEY-01) e Lit (BK-LIT-01)

## Como ver

```bash
php artisan migrate:fresh --seed
php artisan serve
# login admin@hexavale.local / HexaVale!Admin1
# http://127.0.0.1:8000/app
# http://127.0.0.1:8000/estoque/ajuste
```

---

**Pare aqui.** Aprovar explicitamente a Fase 3 para liberar a Fase 4 (compras).
