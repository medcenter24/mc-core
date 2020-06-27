<?php

declare(strict_types=1);

namespace medcenter24\mcCore\App\Foundation;


use Illuminate\Foundation\PackageManifest as BasePackageManifest;

/**
 * Services loaded from the vendor/composer/installed.json (that cached as packages.php)
 * but for me it is not expected, because I've moved vendor to the other directory
 * and autoloader of some libs ties to load wrong paths
 *
 * Class PackageManifest
 * @package medcenter24\mcCore\App\Foundation
 */
class PackageManifest extends BasePackageManifest
{
    private const EXCLUDED_SERVICES = [];

    public function providers()
    {
        $providers = parent::providers();

        // exclude inappropriate services
        return $providers;
    }
}
