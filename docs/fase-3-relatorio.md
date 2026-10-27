# Relatório — Fase 3 (Estoque avançado + shell sem CDN)

**Data:** 2026-10-27  
**Repositório:** https://github.com/douglasmouradev/estoque-hexavale  
**Portão:** aprovado (2026-10-27) — Fase 4 iniciada.

## O que foi feito

- **ADR-0010** — estoque avançado: vencimento, conversão, ajuste, reservas, Idempotency-Key
- **RN-03** — `PoliticaLote` bloqueia saída/reserva de lote vencido
- **RN-04** — `ConversorUnidade` (kg/g, L/mL) na entrada/saída/ajuste
- **RN-09** — `RegistrarAjusteInventario` (motivo + contagem ≥ reservado) + UI `/estoque/ajuste`
- **Reservas** — domínio + HTTP `/estoque/reserva` e `/estoque/reserva/liberar` (Idempotency-Key)
- **RN-18** — middleware `RequireIdempotencyKey` nos POSTs de estoque + `_idempotency_key` nos forms
- **BK-UI-APP-01** — `/app` mantém o protótipo HTML; CDNs removidos (`public/vendor/erp/` + Tailwind build + Source Sans local)

## Suíte (`make quality`)

- lint / types / arch / testes / composer audit — ver execução local

## Fora / próxima fase (4)

- Compras / NF-e / entrada fiscal
- Ordens agrícolas e consumo de reserva na baixa (Fase 5)
- Passkeys UI (BK-PASSKEY-01) e Lit (BK-LIT-01)

## Como ver

```bash
npm run build:erp   # se alterar classes Tailwind do protótipo
php artisan migrate:fresh --seed
php artisan serve
# login admin@hexavale.local / HexaVale!Admin1
# http://127.0.0.1:8000/app
# http://127.0.0.1:8000/estoque/reserva
# http://127.0.0.1:8000/estoque/ajuste
```

---

**Aprovado.** Seguir para `docs/fase-4-relatorio.md`.
