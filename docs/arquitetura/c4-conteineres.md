# C4 — Contêineres

```mermaid
flowchart TB
  Browser[BrowserPWA]
  Caddy[Caddy]
  PHP[PhpFpmLaravel]
  Worker[QueueWorker]
  DB[(MySQL972)]
  Mail[MailpitDev]
  Browser --> Caddy
  Caddy --> PHP
  PHP --> DB
  Worker --> DB
  PHP --> Mail
```
