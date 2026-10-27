# Protótipo de referência

Arquivo canônico: `hexavale_erp_unificado.html`  
Cópia de runtime: `resources/prototipo/hexavale_erp_unificado.html` (servida em **`/app`** após login).

- **Fonte:** HTML unificado enviado pelo produto (`Área de trabalho/2/…`).
- **Runtime atual:** sessão autenticada vê este HTML (Vue/Chart/FA/Tailwind **locais** em `/vendor/erp/`). Conta/admin MFA em `/conta` e `/admin/usuarios`.
- **Assets:** `npm run build:erp` gera `public/vendor/erp/erp.css`; libs em `public/vendor/erp/*.js` e Font Awesome em `webfonts/`. Fontes OFL em `/fonts/source-sans-3-*.woff2` (alias Inter).
- **Módulos embutidos (iframe `srcdoc`):**
  - Ordens de Trabalho — `ot-src` (legado no unificado)
  - Gestão Financeira — `fin-src` a partir de `hexavale-financeiro.html`
- **Pendente (BK-LIT-01):** unificar ao design system Blade/Lit sem mudar fluxos.

Arquivo anterior: `hexavale_erp_unificado.anterior.html`.  
Standalone financeiro: `hexavale-financeiro.html`.
