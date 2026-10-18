-- Usuário de migrations (DDL) separado do usuário da aplicação (DML).
-- Senhas apenas para desenvolvimento local; produção usa secrets.

CREATE USER IF NOT EXISTS 'hexavale_migrate'@'%' IDENTIFIED BY 'hexavale_migrate_secret';
GRANT ALL PRIVILEGES ON hexavale.* TO 'hexavale_migrate'@'%';

-- Aplicação: sem DROP; UPDATE/DELETE em tabelas imutáveis serão bloqueados por triggers (Fase 3).
GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE ON hexavale.* TO 'hexavale_app'@'%';

FLUSH PRIVILEGES;
