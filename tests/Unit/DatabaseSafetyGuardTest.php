<?php

namespace Tests\Unit;

use Tests\TestCase;
use RuntimeException;
use Illuminate\Support\Facades\Config;

class DatabaseSafetyGuardTest extends TestCase
{
    /**
     * Test that Safety Guard throws RuntimeException if target database is changed to keylock_db.
     */
    public function test_guard_aborts_if_database_is_not_keylock_test(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Target database in configuration must be exactly 'keylock_test'");

        Config::set('database.connections.mysql.database', 'keylock_db');
        TestCase::enforceDatabaseSafetyGuard();
    }

    /**
     * Test that Safety Guard throws RuntimeException if username is root.
     */
    public function test_guard_aborts_if_username_is_root(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Database username must NOT be "root"');

        Config::set('database.connections.mysql.username', 'root');
        TestCase::enforceDatabaseSafetyGuard();
    }

    /**
     * Test that Safety Guard throws RuntimeException if environment is not testing.
     */
    public function test_guard_aborts_if_environment_is_not_testing(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('APP_ENV must be exactly "testing"');

        app()->detectEnvironment(fn() => 'production');
        TestCase::enforceDatabaseSafetyGuard();
    }
}
