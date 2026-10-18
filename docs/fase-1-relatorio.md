# Relatório — Fase 1 (Núcleo transversal)

**Data:** 2026-10-18  
**Repositório:** https://github.com/douglasmouradev/estoque-hexavale  
**Portão:** aguardando aprovação humana para iniciar a Fase 2.

## O que foi feito

- **ADRs:** 0007 (MFA TOTP + WebAuthn) e 0008 (cifragem PII / índice cego)
- **Deps:** `spomky-labs/otphp`, `bacon/bacon-qr-code`, `web-auth/webauthn-lib`; `FIELD_ENCRYPTION_KEY` + `FIELD_BLIND_INDEX_KEY`
- **Migrations:** fazendas, users (PII/MFA), RBAC (papéis/permissões/pivô fazenda), dispositivos, credenciais_passkey, auditoria append-only (trigger bloqueia UPDATE/DELETE), ropa_tratamentos, acessos_pii
- **Seed:** ROPA + admin demo (`admin@hexavale.local` / `HexaVale!Admin1`) e operador
- **Domínio (`src/`):** Identidade (Entrar, VerificarTotp, portas), Auditoria (CadeiaHash, EventoAuditoria, registrar), Privacidade (CifradorCampo sodium, MascaradorPii), VOs (UuidV7, Email, Cpf)
- **HTTP:** sessão cookie HttpOnly; `/entrar`, `/entrar/totp`, `/sair`, `/conta`, `/conta/totp`, `/admin/usuarios` (+ revelar CPF com permissão); middlewares fazenda + permissão (nega por padrão)
- **Design:** EtiquetaLote, ReguaNivel, MapaHexTalhao, FichaCarimbo; fontes OFL self-hosted em `/public/fonts/*.woff2` (fecha BK-FONT-01); styleguide atualizado
- **Testes:** unidade (hash/cifra/máscara) + feature (login, TOTP, IDOR fazenda, PII 403, auditoria imutável)

## Versões relevantes

| Componente | Versão |
|---|---|
| PHP | 8.5.x |
| Laravel | 13.37 |
| MySQL (alvo) | 9.7.2 |
| Hash senha | Argon2id |
| otphp / bacon-qr-code / webauthn-lib | via composer.lock |

## Suíte (`make quality`)

- `make lint` — OK  
- `make types` — OK (0 erros, PHPStan max)  
- `make arch` — OK (0 violações Deptrac)  
- `make test` — OK (**15** testes)  
- `composer audit` — sem advisories  

## Backlog atualizado nesta fase

| ID | Status |
|---|---|
| BK-FONT-01 | **fechado** — WOFF2 OFL self-hosted |
| BK-PASSKEY-01 | **aberto** — porta + tabela; cerimônia WebAuthn completa pendente |
| BK-PII-01 | **aberto** — rotação de chave / re-cifrar (job futuro) |
| BK-LIT-01 | aberto (adiado; componentes Blade na Fase 1) |
| BK-SEC-01 | parcial (composer audit OK; gitleaks/Trivy/npm audit pendentes) |

## Demais

- Passkeys: adapter `webauthn-lib` instalado; registro/autenticação completa = BK-PASSKEY-01
- Portal titular LGPD, ledger, ordens, compras, PWA, RAG — fora do escopo (fases seguintes)

## Credenciais demo (somente local)

- Admin: `admin@hexavale.local` / `HexaVale!Admin1`
- Operador: `operador@hexavale.local` (senha no seeder)

---

**Pare aqui.** Aprovar explicitamente a Fase 1 para liberar a Fase 2 (estoque / ledger / protótipo seed).
