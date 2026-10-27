# Relatório — Fase 2 (Cadastros + ledger de estoque)

**Data:** 2026-10-18  
**Repositório:** https://github.com/douglasmouradev/estoque-hexavale  
**Portão:** aguardando aprovação humana para iniciar a Fase 3.

## O que foi feito

- **ADR-0009** — ledger append-only, DECIMAL + `brick/math`, projeção `saldos_lote`
- **Migrations:** `produtos`, `lotes`, `talhoes`, `movimentacoes` (trigger anti UPDATE/DELETE no MySQL), `saldos_lote`
- **Domínio:** `Quantidade`, `SaldoLote` (RN-02), `SituacaoSaldo` (RN-14), `TipoMovimentacao`
- **Casos de uso:** `RegistrarEntrada`, `RegistrarSaida` (RN-07), `ExcluirProduto` (RN-13), idempotency key
- **Seed BK-PROTO-01:** Amistar, Curygen, Fungicida X, Nitrato + talhões N 03 / N 04
- **UI Blade:** `/estoque/saldo`, `/estoque/entrada`, `/estoque/saida` (RBAC `estoque.ler` / `estoque.movimentar`)
- `/app` protótipo HTML permanece como painel legado (sem CDN desde Fase 3 / BK-UI-APP-01)

## Suíte (`make quality`)

- lint / types / arch / **23 testes** / composer audit — OK

## Fora / próxima fase (3)

- RN-03 lote vencido, RN-04 conversão, RN-09 ajuste inventário completo, reservas de ordem
- Substituir `/app` pelo shell real (sem CDN)
- Compras (Fase 4) e ordens agrícolas (Fase 5)

## Como ver

```bash
php artisan migrate:fresh --seed
php artisan serve
# login admin@hexavale.local / HexaVale!Admin1
# http://127.0.0.1:8000/estoque/saldo
```

---

**Pare aqui.** Aprovar explicitamente a Fase 2 para liberar a Fase 3 (regras de estoque avançadas + UI sem CDN).
