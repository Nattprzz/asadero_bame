<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260621000100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Stripe Checkout idempotency key and persisted checkout URL';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE orders ADD COLUMN IF NOT EXISTS checkout_idempotency_key VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE orders ADD COLUMN IF NOT EXISTS stripe_checkout_url VARCHAR(2048) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_ORDERS_CHECKOUT_IDEMPOTENCY_KEY ON orders (checkout_idempotency_key)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IF EXISTS UNIQ_ORDERS_CHECKOUT_IDEMPOTENCY_KEY');
        $this->addSql('ALTER TABLE orders DROP COLUMN checkout_idempotency_key');
        $this->addSql('ALTER TABLE orders DROP COLUMN stripe_checkout_url');
    }
}
