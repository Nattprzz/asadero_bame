<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260620000300 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add optional local assignment to users for responsible and manager authentication responses';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users ADD COLUMN IF NOT EXISTS local_id BIGINT DEFAULT NULL');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_USERS_LOCAL ON users (local_id)');
        $this->addSql(<<<'SQL'
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.table_constraints
        WHERE table_schema = 'public'
          AND table_name = 'users'
          AND constraint_name = 'FK_USERS_LOCAL'
    ) THEN
        ALTER TABLE public.users
            ADD CONSTRAINT FK_USERS_LOCAL
            FOREIGN KEY (local_id) REFERENCES public.locals (id) ON DELETE SET NULL;
    END IF;
END $$;
SQL);

        $this->addSql(<<<'SQL'
UPDATE users
SET local_id = CASE email
    WHEN 'responsable.esparragal@asaderobame.com' THEN 1
    WHEN 'gerente.esparragal@asaderobame.com' THEN 1
    WHEN 'responsable.rnorte@asaderobame.com' THEN 2
    WHEN 'gerente.rnorte@asaderobame.com' THEN 2
    WHEN 'responsable.pmazarron@asaderobame.com' THEN 3
    WHEN 'gerente.pmazarron@asaderobame.com' THEN 3
    WHEN 'responsable.fortuna@asaderobame.com' THEN 4
    WHEN 'gerente.fortuna@asaderobame.com' THEN 4
    WHEN 'responsable.alcantarilla@asaderobame.com' THEN 5
    WHEN 'gerente.alcantarilla@asaderobame.com' THEN 5
    ELSE local_id
END
WHERE email IN (
    'responsable.esparragal@asaderobame.com',
    'gerente.esparragal@asaderobame.com',
    'responsable.rnorte@asaderobame.com',
    'gerente.rnorte@asaderobame.com',
    'responsable.pmazarron@asaderobame.com',
    'gerente.pmazarron@asaderobame.com',
    'responsable.fortuna@asaderobame.com',
    'gerente.fortuna@asaderobame.com',
    'responsable.alcantarilla@asaderobame.com',
    'gerente.alcantarilla@asaderobame.com'
)
SQL);
        $this->addSql("UPDATE users SET local_id = NULL WHERE email = 'admin@asaderobame.com'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users DROP CONSTRAINT IF EXISTS FK_USERS_LOCAL');
        $this->addSql('DROP INDEX IF EXISTS IDX_USERS_LOCAL');
        $this->addSql('ALTER TABLE users DROP COLUMN IF EXISTS local_id');
    }
}
