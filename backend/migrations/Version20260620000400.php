<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260620000400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Synchronize PostgreSQL identity sequences after imported Supabase data';
    }

    public function up(Schema $schema): void
    {
        foreach (['users', 'locals', 'categories', 'products', 'orders', 'order_lines', 'personal_access_tokens', 'password_reset_tokens'] as $table) {
            $this->addSql(sprintf(
                "SELECT setval(pg_get_serial_sequence('%s', 'id'), COALESCE((SELECT MAX(id) FROM %s), 0) + 1, false)",
                $table,
                $table
            ));
        }
    }

    public function down(Schema $schema): void
    {
        // Sequence synchronization is intentionally not reverted.
    }
}
