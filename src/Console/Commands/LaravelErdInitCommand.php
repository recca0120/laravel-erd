<?php

namespace Recca0120\LaravelErd\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Recca0120\LaravelErd\OS;

class LaravelErdInitCommand extends Command
{
    public const ERD_GO_DOWNLOAD_URL = 'https://github.com/kaishuu0123/erd-go/releases/download/v2.0.0/';

    public const DOT_DOWNLOAD_URL = 'https://github.com/kaishuu0123/graphviz-dot.js/releases/download/v0.3.1/';

    protected $signature = 'laravel-erd:init';

    /**
     * @throws ConnectionException
     */
    public function handle(OS $os): int
    {
        $config = config('laravel-erd.binary');

        try {
            $platform = $os->platform();
            $arch = $os->arch();
            $this->downloadErdGo($platform, $arch, $config['erd-go']);
            $this->downloadDot($platform, $arch, $config['dot']);

            return self::SUCCESS;
        } catch (RequestException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    private function downloadErdGo(string $platform, string $arch, string $path): void
    {
        $extension = $platform === OS::WINDOWS ? '.exe' : '';
        $arch = $platform === OS::LINUX && $arch === OS::ARM ? OS::ARM : 'amd64';

        $url = self::ERD_GO_DOWNLOAD_URL.'%s_%s_erd-go%s';
        $this->download(sprintf($url, $platform, $arch, $extension), $path);
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    private function downloadDot(string $platform, string $arch, string $path): void
    {
        $extension = $platform === OS::WINDOWS ? '.exe' : '';
        $arch = $arch === OS::ARM ? '64' : $arch;
        $lookup = [OS::DARWIN => 'macos', OS::WINDOWS => 'win'];

        $url = self::DOT_DOWNLOAD_URL.'graphviz-dot-%s-x%s%s';
        $this->download(sprintf($url, $lookup[$platform] ?? $platform, $arch, $extension), $path);
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    private function download(string $url, string $path): void
    {
        if (File::exists($path)) {
            return;
        }

        $this->line('download: '.$url);
        File::ensureDirectoryExists(dirname($path));
        File::put($path, Http::timeout(300)->get($url)->throw()->body());
        File::chmod($path, 0777);
    }
}
