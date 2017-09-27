<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Foundation;


class Application extends \Illuminate\Foundation\Application
{
    private function isDeploy()
    {
        return !file_exists(__DIR__.'/../../vendor/autoload.php');
    }

    private function getDeployedBootstrapPath($path = '')
    {
        $bsPath = $this->isDeploy() ? realpath(__DIR__ . '/../../../data/bootstrap/')
            : $this->bootstrapPath();

        return $path ? str_replace($this->bootstrapPath(), $this->getDeployedBootstrapPath(), $path) : $bsPath;
    }

    /**
     * Get the path to the cached services.php file.
     *
     * @return string
     */
    public function getCachedServicesPath()
    {
        return $this->getDeployedBootstrapPath(parent::getCachedServicesPath());
    }

    /**
     * Get the path to the cached packages.php file.
     *
     * @return string
     */
    public function getCachedPackagesPath()
    {
        return $this->getDeployedBootstrapPath(parent::getCachedPackagesPath());
    }

    /**
     * Get the path to the configuration cache file.
     *
     * @return string
     */
    public function getCachedConfigPath()
    {
        return $this->getDeployedBootstrapPath(parent::getCachedConfigPath());
    }

    /**
     * Get the path to the routes cache file.
     *
     * @return string
     */
    public function getCachedRoutesPath()
    {
        return $this->getDeployedBootstrapPath(parent::getCachedRoutesPath());
    }
}
