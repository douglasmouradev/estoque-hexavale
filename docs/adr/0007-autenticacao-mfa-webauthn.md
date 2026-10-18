# ADR-0007 — Autenticação, TOTP e WebAuthn

- Status: Aceito
- Data: 2026-10-18

## Contexto

Fase 1 exige autenticação forte, MFA e passkeys, sem CDN e com domínio isolado do framework.

## Decisão

- Sessão web Laravel (cookie `HttpOnly`, `Secure` em produção, `SameSite=Lax`).
- Senha: **Argon2id** (`HASH_DRIVER=argon2id`).
- TOTP: `spomky-labs/otphp` + QR `bacon/bacon-qr-code`.
- Passkeys: `web-auth/webauthn-lib` (registro/assertação de um dispositivo).
- Casos de uso em `src/Identidade`; controllers Blade são adaptadores.

## Consequências

+ Stack madura e testável.
− WebAuthn exige `APP_URL` estável (rpId); em local usar host consistente.
