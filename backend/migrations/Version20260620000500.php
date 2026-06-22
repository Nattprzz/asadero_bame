<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260620000500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add payment method to customer orders';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE orders ADD COLUMN IF NOT EXISTS payment_method VARCHAR(40) DEFAULT 'card' NOT NULL");
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_ORDERS_PAYMENT_METHOD ON orders (payment_method)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IF EXISTS IDX_ORDERS_PAYMENT_METHOD');
        $this->addSql('ALTER TABLE orders DROP COLUMN IF EXISTS payment_method');
    }
}
