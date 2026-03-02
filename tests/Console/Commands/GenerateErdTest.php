<?php

namespace Recca0120\LaravelErd\Tests\Console\Commands;

use Illuminate\Support\Facades\File;
use PDO;
use Recca0120\LaravelErd\Tests\TestCase;

class GenerateErdTest extends TestCase
{
    private string $storagePath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->storagePath = realpath(__DIR__.'/../../Fixtures');
        $this->app['config']->set('laravel-erd.binary', [
            'erd-go' => $this->storagePath.'/bin/erd-go',
            'dot' => $this->storagePath.'/bin/dot',
        ]);
        $this->app['config']->set('laravel-erd.storage_path', $this->storagePath);
        $this->app['config']->set([
            'cache.default' => 'database',
            'passport.storage.database.connection' => 'testbench',
            'telescope.storage.database.connection' => 'testbench',
        ]);

        $this->app['config']->set('database.default', 'testbench');
        $this->app['config']->set('database.connections.testbench', [
            'driver' => 'mysql',
            'url' => '',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'forge',
            'username' => 'forge',
            'password' => '',
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => null,
            ]) : [],
        ]);
    }

    protected function tearDown(): void
    {
        $this->cachedTestMigratorProcessors = [];
        self::assertEquals([
            'driver' => 'mysql',
            'url' => '',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'forge',
            'username' => 'forge',
            'password' => '',
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => null,
            ]) : [],
        ], config('database.connections.testbench'));

        parent::tearDown();
    }

    public function test_generate_testbench_svg(): void
    {
        $file = 'testbench.svg';

        $parameters = $this->givenParameters(['--file' => $file]);
        $this->artisan('erd:generate', $parameters)->execute();

        $contents = file_get_contents($this->storagePath.'/'.$file);
        self::assertStringContainsString('<!-- cars -->', $contents);
        self::assertStringContainsString('<!-- phones&#45;&#45;users -->', $contents);
    }

    public function test_customize_fake_database(): void
    {
        $file = 'testbench.svg';

        $this->app['config']->set('laravel-erd.connections.testbench', [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'test',
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => [],
        ]);

        $parameters = $this->givenParameters(['--file' => $file]);
        $this->artisan('erd:generate', $parameters)->execute();

        $contents = file_get_contents($this->storagePath.'/'.$file);
        self::assertStringContainsString('<!-- cars -->', $contents);
        self::assertStringContainsString('<!-- phones&#45;&#45;users -->', $contents);
    }

    public function test_pgsql_connection_is_replaced_with_sqlite(): void
    {
        $this->app['config']->set('database.default', 'pgsql_test');
        $this->app['config']->set('database.connections.pgsql_test', [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'port' => '59999',
            'database' => 'nonexistent',
            'username' => 'nonexistent',
            'password' => 'nonexistent',
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ]);

        $file = 'pgsql_test.svg';
        $parameters = $this->givenParameters(['--file' => $file]);
        $this->artisan('erd:generate', $parameters)->execute();

        $contents = file_get_contents($this->storagePath.'/'.$file);
        self::assertStringContainsString('<!-- cars -->', $contents);

        // Verify pgsql config is restored after generation
        self::assertEquals('pgsql', config('database.connections.pgsql_test.driver'));
    }

    public function test_customize_fake_database_with_pgsql(): void
    {
        if (! env('PGSQL_HOST')) {
            self::markTestSkipped('PostgreSQL is not available');
        }

        $file = 'pgsql_custom.svg';

        $this->app['config']->set('database.default', 'pgsql_test');
        $this->app['config']->set('database.connections.pgsql_test', [
            'driver' => 'pgsql',
            'host' => env('PGSQL_HOST', '127.0.0.1'),
            'port' => env('PGSQL_PORT', '5432'),
            'database' => 'nonexistent',
            'username' => 'nonexistent',
            'password' => 'nonexistent',
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ]);

        $this->app['config']->set('laravel-erd.connections.pgsql_test', [
            'driver' => 'pgsql',
            'host' => env('PGSQL_HOST', '127.0.0.1'),
            'port' => env('PGSQL_PORT', '5432'),
            'database' => env('PGSQL_DATABASE', 'testbench'),
            'username' => env('PGSQL_USERNAME', 'root'),
            'password' => env('PGSQL_PASSWORD', 'root'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ]);

        $parameters = $this->givenParameters(['--file' => $file]);
        $this->artisan('erd:generate', $parameters)->execute();

        $contents = file_get_contents($this->storagePath.'/'.$file);
        self::assertStringContainsString('<!-- cars -->', $contents);
    }

    public function test_generate_other_svg(): void
    {
        $file = 'other.svg';

        $parameters = $this->givenParameters(['database' => File::name($file), '--file' => $file]);
        $this->artisan('erd:generate', $parameters)->execute();

        $contents = file_get_contents($this->storagePath.'/'.$file);
        self::assertStringContainsString('<!-- other_cars -->', $contents);
    }

    public function test_command_not_exists(): void
    {
        $originalPath = getenv('PATH');
        putenv('PATH=/nonexistent');
        $this->app['config']->set('laravel-erd.binary.erd-go', '/bin/erd-go');

        try {
            $this->artisan(
                'erd:generate',
                $this->givenParameters(['--graceful' => true])
            )->assertFailed();
        } finally {
            putenv("PATH=$originalPath");
        }
    }

    private function givenParameters(array $attributes = []): array
    {
        return array_merge([
            '--file' => 'default.svg',
            '--directory' => $this->storagePath,
            '--path' => '../../../../database/migrations',
        ], $attributes);
    }
}
