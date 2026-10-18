# Matriz RBAC (esboço)

Perfis: Operador de campo, Almoxarife, Comprador, Aprovador, Responsável técnico, Gestor, Mecânico, Auditor (RO), DPO, Administrador de TI.

Escopo: por `fazenda_id`. Negar por padrão.

| Perfil | Estoque escrita | Liberar ordem | Aprovar compra | PII revelar | Admin |
|---|---|---|---|---|---|
| Operador | limitado | não | não | não | não |
| Almoxarife | sim | não | não | não | não |
| Comprador | não | não | solicitar | não | não |
| Aprovador | não | não | sim* | não | não |
| RT | leitura técnica | sim | não | não | não |
| Gestor | sim | sim | sim* | com motivo | não |
| Mecânico | frota | não | não | não | não |
| Auditor | RO | RO | RO | não | não |
| DPO | — | — | — | painel privacidade | parcial |
| Admin TI | — | — | — | — | sim |

\* Solicitante ≠ aprovador (RN-12). Testes automatizados por perfil na Fase 1.
