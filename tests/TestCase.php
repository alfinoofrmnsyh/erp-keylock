<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Enforce strict multi-layer fail-fast safety guard before any test or migration runs.
     *
     * @param \Illuminate\Foundation\Application|null $app
     * @return void
     * @throws \RuntimeException
     */
    public static function enforceDatabaseSafetyGuard($app = null): void
    {
        $app = $app ?? app();

        // 1. Refuse to run if Laravel configuration is cached
        if ($app->configurationIsCached()) {
            throw new \RuntimeException(
                'CRITICAL SAFETY GUARD: Laravel configuration cache is active! Running tests with cached configuration can overwrite test settings and wipe operational data. Please run "php artisan config:clear" first.'
            );
        }

        // 2. Refuse to run if APP_ENV is not exactly 'testing'
        if ($app->environment() !== 'testing') {
            throw new \RuntimeException(
                'CRITICAL SAFETY GUARD: APP_ENV must be exactly "testing"! Current: ' . $app->environment()
            );
        }

        // 3. Refuse to run if default connection is not mysql
        $default = config('database.default');
        if ($default !== 'mysql') {
            throw new \RuntimeException(
                'CRITICAL SAFETY GUARD: Default database driver must be "mysql"! Current: ' . $default
            );
        }

        // 4. Refuse to run if database name in config is not exactly 'keylock_test'
        $configDb = config("database.connections.{$default}.database");
        if ($configDb !== 'keylock_test') {
            throw new \RuntimeException(
                "CRITICAL SAFETY GUARD: Target database in configuration must be exactly 'keylock_test'! Current: '{$configDb}'. Execution aborted to protect operational data."
            );
        }

        // 5. Refuse to run if testing as root (least privilege violation)
        $configUser = config("database.connections.{$default}.username");
        if ($configUser === 'root') {
            throw new \RuntimeException(
                'CRITICAL SAFETY GUARD: Database username must NOT be "root"! Must use dedicated least-privilege user "keylock_tester".'
            );
        }

        // 6. Refuse to run if host and port are not local test target
        $host = config("database.connections.{$default}.host");
        $port = config("database.connections.{$default}.port");
        if (!in_array($host, ['127.0.0.1', 'localhost']) || (string)$port !== '3306') {
            throw new \RuntimeException(
                "CRITICAL SAFETY GUARD: Host and port must be local 127.0.0.1:3306! Current: {$host}:{$port}"
            );
        }

        // 7. Verify live connection name directly from PDO
        try {
            $connection = \Illuminate\Support\Facades\DB::connection();
            $actualDb = $connection->getDatabaseName();
            if ($actualDb !== 'keylock_test') {
                throw new \RuntimeException(
                    "CRITICAL SAFETY GUARD: Active database connection resolved to '{$actualDb}', which is NOT 'keylock_test'! Test halted immediately to protect operational data."
                );
            }
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'CRITICAL SAFETY GUARD')) {
                throw $e;
            }
            throw new \RuntimeException(
                'CRITICAL SAFETY GUARD: Could not securely verify active database connection: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Hook before RefreshDatabase executes migrate:fresh
     */
    protected function beforeRefreshingDatabase()
    {
        static::enforceDatabaseSafetyGuard($this->app);
    }

    /**
     * Setup the test environment
     */
    protected function setUp(): void
    {
        parent::setUp();
        static::enforceDatabaseSafetyGuard($this->app);
    }
}
