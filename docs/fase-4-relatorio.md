# Relatório — Fase 4 (Compras + NF-e chave)

**Data:** 2026-10-27  
**Repositório:** https://github.com/douglasmouradev/estoque-hexavale  
**Portão:** aprovado (2026-10-27) — Fase 5 iniciada.

## O que foi feito

- **ADR-0011** — compras: fluxo de pedido, RN-12, chave NF-e
- **RN-12** — `PoliticaAprovacao` impede autoaprovação
- **PedidoCompra** — criar / solicitar / aprovar / rejeitar / receber
- **Recebimento** — chave 44 dígitos + entrada no ledger por item
- **RBAC** — `compras.ler|solicitar|aprovar|receber` + usuários demo comprador/aprovador
- **UI** — `/compras`, `/compras/novo`, detalhe com ações

## Suíte (`make quality`)

- lint / types / arch / testes / composer audit — ver execução local

## Fora / próxima fase (5)

- Ordens agrícolas, reserva na liberação, baixa automática (RN-01/05/06/08/20)
- Parse XML NF-e (evolução Q-04)
- Passkeys UI (BK-PASSKEY-01) e Lit (BK-LIT-01)

## Como ver

```bash
php artisan migrate:fresh --seed
php artisan serve
# comprador@hexavale.local / HexaVale!Comp1  → criar/solicitar
# aprovador@hexavale.local / HexaVale!Apr1   → aprovar
# admin@hexavale.local / HexaVale!Admin1     → receber
# http://127.0.0.1:8000/compras
```

---

**Aprovado.** Seguir para `docs/fase-5-relatorio.md`.
