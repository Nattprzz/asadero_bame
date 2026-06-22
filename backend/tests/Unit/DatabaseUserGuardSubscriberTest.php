<?php

namespace App\Tests\Unit;

use App\EventSubscriber\DatabaseUserGuardSubscriber;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class DatabaseUserGuardSubscriberTest extends TestCase
{
    #[DataProvider('productionUsers')]
    public function testProductionDatabaseUserGuard(string $databaseUrl, bool $expected): void
    {
        self::assertSame($expected, DatabaseUserGuardSubscriber::isAllowedProductionDatabaseUser($databaseUrl));
    }

    public static function productionUsers(): array
    {
        return [
            'runtime app user' => ['postgresql://bame_app:CHANGE_ME@host:5432/postgres', true],
            'local admin user' => ['postgresql://admin:secret@127.0.0.1:5432/postgres', true],
            'postgres user' => ['postgresql://postgres:secret@host:5432/postgres', false],
            'empty user' => ['postgresql://:secret@host:5432/postgres', false],
            'malformed' => ['not-a-url', false],
        ];
    }
}
