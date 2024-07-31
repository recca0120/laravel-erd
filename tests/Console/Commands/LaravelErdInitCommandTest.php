<?php

namespace Recca0120\LaravelErd\Tests\Console\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use Recca0120\LaravelErd\Console\Commands\LaravelErdInitCommand;
use Recca0120\LaravelErd\OS;
use Recca0120\LaravelErd\Tests\TestCase;

class LaravelErdInitCommandTest extends TestCase
{
    /**
     * @dataProvider osProvider
     */
    #[DataProvider('osProvider')]
    public function test_download_binary(string $platform, string $arch, array $expected): void
    {
        File::spy();
        File::expects('exists')->andReturn(false)->twice();
        Http::fake(['*' => Http::response('ok')]);

        $this->givenOs($platform, $arch);

        $this->artisan('laravel-erd:init')
            ->assertSuccessful()
            ->execute();

        $recorded = Http::recorded();

        [$request] = $recorded[0];
        self::assertEquals(LaravelErdInitCommand::ERD_GO_DOWNLOAD_URL.$expected['erd-go'], $request->url());

        [$request] = $recorded[1];
        self::assertEquals(LaravelErdInitCommand::DOT_DOWNLOAD_URL.$expected['dot'], $request->url());
    }

    public static function osProvider(): array
    {
        return [
            [
                'platform' => OS::DARWIN,
                'arch' => OS::ARM,
                'expected' => [
                    'erd-go' => 'darwin_amd64_erd-go',
                    'dot' => 'graphviz-dot-macos-x64',
                ],
            ],
            [
                'platform' => OS::DARWIN,
                'arch' => '64',
                'expected' => [
                    'erd-go' => 'darwin_amd64_erd-go',
                    'dot' => 'graphviz-dot-macos-x64',
                ],
            ],
            [
                'platform' => OS::LINUX,
                'arch' => OS::ARM,
                'expected' => [
                    'erd-go' => 'linux_arm_erd-go',
                    'dot' => 'graphviz-dot-linux-x64',
                ],
            ],
            [
                'platform' => OS::LINUX,
                'arch' => '64',
                'expected' => [
                    'erd-go' => 'linux_amd64_erd-go',
                    'dot' => 'graphviz-dot-linux-x64',
                ],
            ],
            [
                'platform' => OS::WINDOWS,
                'arch' => '64',
                'expected' => [
                    'erd-go' => 'windows_amd64_erd-go.exe',
                    'dot' => 'graphviz-dot-win-x64.exe',
                ],
            ],
        ];
    }

    private function givenOs(string $platform, string $arch): void
    {
        $os = Mockery::mock(OS::class);
        $os->expects('platform')->andReturn($platform);
        $os->expects('arch')->andReturn($arch);
        $this->swap(OS::class, $os);
    }
}
