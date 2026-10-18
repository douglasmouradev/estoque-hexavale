# ADR-0008 — Cifragem de campo PII

- Status: Aceito
- Data: 2026-10-18

## Contexto

LGPD exige proteção de CPF, telefone e e-mail pessoal em repouso, com busca eventual.

## Decisão

- AES-256-GCM via **libsodium** (`sodium_crypto_secretbox`).
- Chave dedicada `FIELD_ENCRYPTION_KEY` (32 bytes em base64), distinta de `APP_KEY`.
- Índice cego: HMAC-SHA256 truncado com chave derivada (`FIELD_BLIND_INDEX_KEY`).
- Mascaramento na UI por padrão; revelação exige permissão `pii.revelar` + motivo → `acessos_pii`.

## Consequências

+ Envelope simples sem KMS externo no MVP.
− Rotação de chave exige re-cifrar (job futuro — BK-PII-01).
