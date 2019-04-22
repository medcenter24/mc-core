<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

namespace medcenter24\mcCore\App\Helpers;


use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FileHelper
{
    public static function isDirExists(string $path): bool
    {
        return file_exists($path) && is_dir($path);
    }

    public static function createDir(string $path): bool
    {
        return self::isDirExists($path) ? true : mkdir($path, 0764, true);
    }

    /**
     * creates directories recursively
     * @example to create `/www/data/log` => $path = [www, data, log]
     *
     * @param array $paths
     * @return bool
     */
    public static function createDirRecursive(array $paths = []): bool
    {
        $dir = '';
        foreach ($paths as $path) {
            $dir .= DIRECTORY_SEPARATOR . trim($path, DIRECTORY_SEPARATOR);
            if (!self::isDirExists($dir)) {
                self::createDir($dir);
            }
        }

        return true;
    }

    public static function isWritable(string $path): bool
    {
        return file_exists($path) && is_writable($path);
    }

    public static function isReadable(string $path): bool
    {
        return file_exists($path) && is_readable($path);
    }

    public static function getContent(string $path): string
    {
        return file_get_contents($path);
    }

    public static function writeConfig(string $path, array $params=[]): bool
    {
        $config = var_export($params, true);
        return file_put_contents($path, "<?php return $config ;");
    }

    public static function writeFile(string $path, string $content): bool
    {
        return file_put_contents($path, $content);
    }

    /**
     * php delete function that deals with directories recursively
     * @param $target
     * @return bool
     */
    public static function delete($target): bool
    {
        if (file_exists($target)) {
            $di = new RecursiveDirectoryIterator($target, FilesystemIterator::SKIP_DOTS);
            $ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
            foreach ( $ri as $file ) {
                $file->isDir() ?  rmdir($file) : unlink($file);
            }
            $di->isDir() ? rmdir($target) : unlink($target);
        }
        return true;
    }
}
