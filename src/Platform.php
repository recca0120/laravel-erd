<?php

namespace Recca0120\LaravelErd;

class Platform
{
    public const DARWIN = 'darwin';

    public const LINUX = 'linux';

    public const WINDOWS = 'windows';

    public const ARM = 'arm';

    public function platform(): string
    {
        $platform = strtolower(PHP_OS);
        if (str_contains($platform, self::DARWIN)) {
            return self::DARWIN;
        }

        if (str_contains($platform, 'win')) {
            return self::WINDOWS;
        }

        return self::LINUX;
    }

    public function arch(): string
    {
        $name = php_uname('m');

        if (stripos($name, 'aarch64') !== false || stripos($name, 'arm64') !== false) {
            return self::ARM;
        }

        return str_contains($name, '64') ? '64' : '32';
    }
}
