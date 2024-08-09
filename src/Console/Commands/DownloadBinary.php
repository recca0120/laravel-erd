<?php

namespace Recca0120\LaravelErd\Console\Commands;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Http\Client\Common\Plugin\ErrorPlugin;
use Http\Client\Common\Plugin\RedirectPlugin;
use Http\Client\Common\PluginClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Recca0120\LaravelErd\OS;

class DownloadBinary extends Command
{
    public const ERD_GO_DOWNLOAD_URL = 'https://github.com/kaishuu0123/erd-go/releases/download/v2.0.0/';

    public const DOT_DOWNLOAD_URL = 'https://github.com/kaishuu0123/graphviz-dot.js/releases/download/v0.3.1/';

    protected $signature = 'erd:download';

    private ClientInterface $client;

    private OS $os;

    public function __construct(ClientInterface $client, OS $os)
    {
        parent::__construct();
        $this->client = $client;
        $this->os = $os;
    }

    public function handle(): int
    {
        $config = config('laravel-erd.binary');

        try {
            $platform = $this->os->platform();
            $arch = $this->os->arch();
            $this->downloadErdGo($platform, $arch, $config['erd-go']);
            $this->downloadDot($platform, $arch, $config['dot']);

            return self::SUCCESS;
        } catch (ClientExceptionInterface $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }

    /**
     * @throws ClientExceptionInterface
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
     * @throws ClientExceptionInterface
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
     * @throws ClientExceptionInterface
     */
    private function download(string $url, string $path): void
    {
        if (File::exists($path)) {
            return;
        }

        $this->line('download: '.$url);
        File::ensureDirectoryExists(dirname($path));

        $request = new Request('GET', $url);
        $response = (new PluginClient($this->client, [
            new ErrorPlugin(),
            new RedirectPlugin(),
        ]))->sendRequest($request);

        File::put($path, (string) $response->getBody());
        File::chmod($path, 0777);
    }
}
