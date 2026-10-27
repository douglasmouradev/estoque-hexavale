# Relatório — Fase 5 (Ordens agrícolas)

**Data:** 2026-10-27  
**Repositório:** https://github.com/douglasmouradev/estoque-hexavale  
**Portão:** aguardando aprovação humana para iniciar a Fase 6.

## O que foi feito

- **ADR-0012** — ciclo de ordem agrícola + reserva/baixa
- **RN-01** — rascunho sem movimento; liberar reserva; cancelar com motivo
- **RN-05** — `FormulaDose` (dose×ha; pulverização opcional /100 L calda)
- **RN-06** — área tratada ∈ (0, planejada]
- **RN-08** — baixa única a partir de liberada
- **RN-20** — campos: tipo, talhão, área, dose, lote, observação
- **UI** — `/operacao` (listar / criar / liberar / baixar / cancelar)
- **ConsumirReserva** — `baixa_reserva` + `saida` no ledger

## Suíte (`make quality`)

- lint / types / arch / testes / composer audit — ver execução local

## Fora / próxima fase (6)

- RN-10/11 (correção versionada / conferência bula)
- Relatórios RN-15/19 e frota RN-16
- Passkeys UI (BK-PASSKEY-01) e Lit (BK-LIT-01)

## Como ver

```bash
php artisan migrate:fresh --seed
php artisan serve
# admin@hexavale.local / HexaVale!Admin1
# http://127.0.0.1:8000/operacao
```

---

**Pare aqui.** Aprovar explicitamente a Fase 5 para liberar a Fase 6 (relatórios / frota).
