<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260617000200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Stripe payment fields to customer orders';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE orders ADD payment_status VARCHAR(40) DEFAULT 'pending' NOT NULL");
        $this->addSql('ALTER TABLE orders ADD stripe_checkout_session_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE orders ADD paid_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_ORDERS_STRIPE_CHECKOUT_SESSION ON orders (stripe_checkout_session_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IF EXISTS UNIQ_ORDERS_STRIPE_CHECKOUT_SESSION');
        $this->addSql('ALTER TABLE orders DROP payment_status');
        $this->addSql('ALTER TABLE orders DROP stripe_checkout_session_id');
        $this->addSql('ALTER TABLE orders DROP paid_at');
    }
}
