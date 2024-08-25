<?php

namespace Recca0120\LaravelErd\Tests\Console\Commands;

use Illuminate\Support\Facades\File;
use PDO;
use Recca0120\LaravelErd\Tests\TestCase;

class GenerateErdTest extends TestCase
{
    private string $storagePath;

    private string $file = 'actual_artisan.svg';

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

    public function test_generate_default_svg(): void
    {
        $file = 'testbench.svg';

        $parameters = $this->givenParameters(['--file' => $file]);
        $this->artisan('erd:generate', $parameters)->execute();

        $contents = file_get_contents($this->storagePath.'/'.$file);
        self::assertStringContainsString('<!-- cars -->', $contents);
        self::assertStringContainsString('<!-- phones&#45;&#45;users -->', $contents);
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
        $this->app['config']->set('laravel-erd.binary.erd-go', '/bin/erd-go');

        $this->artisan(
            'erd:generate',
            $this->givenParameters(['--graceful' => true])
        )->assertFailed();
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
