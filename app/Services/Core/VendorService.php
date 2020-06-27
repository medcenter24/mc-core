<?php

declare(strict_types=1);

namespace medcenter24\mcCore\App\Services\Core;


class VendorService
{
    private string $path;

    private static ?VendorService $instance = null;

    private function __construct($path = '')
    {
        $this->path = $path;
    }

    public static function instance(string $path = ''): VendorService
    {
        if (!self::$instance) {
            self::$instance = new self($path);
        }
        return self::$instance;
    }

    public function getVendorPath(): string
    {
        return $this->path;
    }
}
