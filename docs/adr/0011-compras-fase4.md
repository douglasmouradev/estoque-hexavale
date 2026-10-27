# ADR-0011 — Compras (Fase 4)

- Status: Aceito
- Data: 2026-10-27

## Contexto

Fase 3 fechou estoque avançado. Faltava pedido de compra com segregação de aprovação e vínculo fiscal mínimo.

## Decisão

- Contexto `Compras` com fluxo `rascunho → solicitado → aprovado|rejeitado → recebido`.
- RN-12 via `PoliticaAprovacao` (solicitante ≠ aprovador) em aprovar/rejeitar.
- Q-04 MVP: chave NF-e 44 dígitos (`ChaveNfe`); XML/SEFAZ ficam para depois.
- Recebimento cria/reusa lote e chama `RegistrarEntrada` (ledger append-only).
- Permissões: `compras.ler`, `compras.solicitar`, `compras.aprovar`, `compras.receber`.

## Consequências

+ Compra rastreável até o estoque sem autoaprovação.
− Sem parse de XML NF-e nem cadastro rico de fornecedor nesta fase.
