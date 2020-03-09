<?php

namespace medcenter24\mcCore\App\Services\Core;

use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Helpers\FileHelper;

class ExtensionManagerService
{
    /**
     * Checks that extension installed
     * @param string $extName
     * @return bool
     */
    public function has(string $extName = ''): bool
    {
        $status = false;
        $modulesFilePath = app_path() . '/../modules_statuses.json';
        try {
            $modules = json_decode(FileHelper::getContent($modulesFilePath), 1);
            $status = array_key_exists($extName, $modules) && $modules[$extName];
        } catch (InconsistentDataException $e) {}
        return $status;
    }
}
