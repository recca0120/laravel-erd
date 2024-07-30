<?php

namespace Recca0120\LaravelErd\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class LaravelErdInitCommand extends Command
{
    private const DARWIN = 'darwin';

    private const ARM = 'arm';

    protected $signature = 'laravel-erd:init';

    public function handle(): int
    {
        $os = $this->os();
        $arch = $this->arch();
        $config = config('laravel-erd.binary');

        try {
            $this->downloadErdGo($os, $arch, $config['erd-go']);
            $this->downloadDot($os, $arch, $config['dot']);

            return self::SUCCESS;
        } catch (RequestException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }

    /**
     * @throws RequestException
     */
    private function downloadErdGo(string $os, string $arch, string $path): void
    {
        $os = $os === self::DARWIN && $arch === self::ARM ? 'linux' : $os;
        $arch = $arch === self::ARM ? $arch : 'amd'.$arch;
        $url = "https://github.com/kaishuu0123/erd-go/releases/download/v2.0.0/{$os}_{$arch}_erd-go";
        $this->download($url, $path);
    }

    /**
     * @throws RequestException
     */
    private function downloadDot(string $os, string $arch, string $path): void
    {
        $os = $os === self::DARWIN ? 'macos' : $os;
        $arch = $arch === self::ARM ? '64' : $arch;
        $url = "https://github.com/kaishuu0123/graphviz-dot.js/releases/download/v0.3.1/graphviz-dot-{$os}-x{$arch}";
        $this->download($url, $path);
    }

    /**
     * @throws RequestException
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

    private function arch(): string
    {
        $name = php_uname('m');

        if (stripos($name, 'aarch64') !== false || stripos($name, 'arm64') !== false) {
            return self::ARM;
        }

        return strpos($name, '64') !== false ? '64' : '32';
    }

    private function os(): string
    {
        $os = strtolower(PHP_OS);
        if (strpos($os, self::DARWIN) !== false) {
            return $os;
        }

        if (strpos($os, 'win') !== false) {
            return 'windows';
        }

        return 'linux';
    }
}
