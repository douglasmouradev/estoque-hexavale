# Protótipo de referência

Arquivo canônico: `hexavale_erp_unificado.html`  
Cópia de runtime: `resources/prototipo/hexavale_erp_unificado.html` (servida em **`/app`** após login).

- **Fonte:** HTML unificado enviado pelo produto (`Área de trabalho/2/…`).
- **Runtime atual:** sessão autenticada vê este HTML **exatamente** (Vue/Tailwind via CDN do arquivo). Conta/admin MFA em `/conta` e `/admin/usuarios`.
- **Módulos embutidos (iframe `srcdoc`):**
  - Ordens de Trabalho — `ot-src` (legado no unificado)
  - Gestão Financeira — `fin-src` a partir de `hexavale-financeiro.html` (`Área de trabalho/2/hexavale-financeiro (1).html`)
- **Pendência:** substituir CDNs e unificar ao design system (BK-UI-APP-01) sem mudar fluxos.

Arquivo anterior: `hexavale_erp_unificado.anterior.html`.  
Standalone financeiro: `hexavale-financeiro.html`.
