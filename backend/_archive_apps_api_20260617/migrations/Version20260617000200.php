<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260617000200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add optional username to users for login by email or username';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users ADD username VARCHAR(80) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_USERS_USERNAME ON users (username)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IF EXISTS UNIQ_USERS_USERNAME');
        $this->addSql('ALTER TABLE users DROP username');
    }
}
