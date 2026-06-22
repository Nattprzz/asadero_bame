<?php

namespace App\Tests\Unit;

use App\Tests\Functional\ApiTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TestDatabaseGuardTest extends TestCase
{
    #[DataProvider('databaseUrls')]
    public function testDatabaseUrlSafety(string $databaseUrl, bool $expected): void
    {
        self::assertSame($expected, ApiTestCase::isSafeTestDatabaseUrl($databaseUrl));
    }

    public static function databaseUrls(): array
    {
        return [
            'localhost' => ['postgresql://app:app@localhost:5432/bame_test', true],
            'loopback ip' => ['postgresql://app:app@127.0.0.1:5432/bame_test', true],
            'docker postgres' => ['postgresql://app:app@postgres:5432/bame_test', true],
            'docker db' => ['postgresql://app:app@db:5432/bame_test', true],
            'docker database' => ['postgresql://app:app@database:5432/bame_test', true],
            'supabase host' => ['postgresql://app:app@db.supabase.co:5432/bame_test', false],
            'pooler host' => ['postgresql://app:app@pooler.supabase.com:5432/bame_test', false],
            'remote host' => ['postgresql://app:app@example.com:5432/bame_test', false],
            'empty host' => ['postgresql://app:app@/bame_test', false],
            'malformed' => ['not-a-url', false],
        ];
    }
}
