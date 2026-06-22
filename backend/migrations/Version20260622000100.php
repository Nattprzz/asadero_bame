<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260622000100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add local product availability and reservation hours';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE local_product ADD available BOOLEAN DEFAULT TRUE NOT NULL');
        $this->addSql("ALTER TABLE locals ADD reservation_hours JSONB DEFAULT '{}' NOT NULL");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE local_product DROP available');
        $this->addSql('ALTER TABLE locals DROP reservation_hours');
    }
}
