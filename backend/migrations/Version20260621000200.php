<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260621000200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add persisted Stripe event ledger for webhook idempotency and auditability';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE stripe_event_ledger (
                id BIGSERIAL NOT NULL,
                stripe_event_id VARCHAR(255) NOT NULL,
                type VARCHAR(255) NOT NULL,
                status VARCHAR(40) NOT NULL,
                payload JSONB NOT NULL,
                processed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
                error_message TEXT DEFAULT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                PRIMARY KEY(id)
            )
        SQL);

        $this->addSql('CREATE UNIQUE INDEX UNIQ_STRIPE_EVENT_LEDGER_EVENT_ID ON stripe_event_ledger (stripe_event_id)');
        $this->addSql('CREATE INDEX IDX_STRIPE_EVENT_LEDGER_STATUS ON stripe_event_ledger (status)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IF EXISTS IDX_STRIPE_EVENT_LEDGER_STATUS');
        $this->addSql('DROP INDEX IF EXISTS UNIQ_STRIPE_EVENT_LEDGER_EVENT_ID');
        $this->addSql('DROP TABLE stripe_event_ledger');
    }
}
