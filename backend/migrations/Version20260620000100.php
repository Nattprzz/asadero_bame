<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260620000100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add operational indexes for orders and token cleanup';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_ORDERS_STATUS_CREATED_AT ON orders (status, created_at)');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_ORDERS_PAYMENT_STATUS ON orders (payment_status)');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_PERSONAL_ACCESS_TOKENS_EXPIRY_REVOKED ON personal_access_tokens (expires_at, revoked_at)');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_PASSWORD_RESET_TOKENS_EXPIRES_AT ON password_reset_tokens (expires_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IF EXISTS IDX_PASSWORD_RESET_TOKENS_EXPIRES_AT');
        $this->addSql('DROP INDEX IF EXISTS IDX_PERSONAL_ACCESS_TOKENS_EXPIRY_REVOKED');
        $this->addSql('DROP INDEX IF EXISTS IDX_ORDERS_PAYMENT_STATUS');
        $this->addSql('DROP INDEX IF EXISTS IDX_ORDERS_STATUS_CREATED_AT');
    }
}
