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
use Illuminate\Support\Str;
use medcenter24\mcCore\App\Exceptions\CommonException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Exception;

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
            if (is_dir($target)) {
                $di = new RecursiveDirectoryIterator($target, FilesystemIterator::SKIP_DOTS);
                $ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
            } else {
                $di = false;
                $ri = [new SplFileInfo($target)];
            }
            foreach ( $ri as $file ) {
                $file->isDir() ?  rmdir($file) : unlink($file);
            }
            if ($di) {
                $di->isDir() ? rmdir($target) : unlink($target);
            }
        }
        return true;
    }

    /**
     * Checks that file match excluded masks
     * @param SplFileInfo $fileInfo
     * @param array $rules
     * @return bool
     */
    private static function isExcluded(SplFileInfo $fileInfo, array $rules): bool
    {
        $excluded = false;
        foreach($rules as $key => $rule) {
            if ($key === 'startsWith') {
                $fileName = $fileInfo->getFilename();
                $excluded = Str::startsWith($fileName, $rule);
            }
        }
        return $excluded;
    }

    /**
     * Calculates size
     * @param string $path
     * @param array $extensions
     * @param array $excludeRules
     *  'startsWith' - the files that starts with string
     * @return int
     */
    public static function getSize(string $path, array $extensions = [], $excludeRules = []): int
    {
        $bytes = 0;
        self::mapFiles($path, static function (SplFileInfo $fileInfo) use (&$bytes) {
            $bytes += $fileInfo->getSize();
        }, $extensions, $excludeRules);
        return $bytes;
    }

    public static function filesCount(string $path, array $extensions = [], $excludeRules = []): int
    {
        $count = 0;
        self::mapFiles($path, static function (SplFileInfo $fileInfo) use (&$count) {
            $count++;
        }, $extensions, $excludeRules);
        return $count;
    }

    public static function mapFiles(string $path, $closure, array $extensions = [], $excludeRules = []): void
    {
        $path = realpath($path);
        if ($path !== false && $path !== '' && file_exists($path)) {
            if (is_dir($path)) {
                /** @var SplFileInfo $object */
                foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path,
                    FilesystemIterator::SKIP_DOTS)) as $object) {
                    if (
                        static::isExpectedExtension($object, $extensions)
                        && !static::isExcluded($object, $excludeRules)
                    ) {
                        $stat = $closure($object);

                        if ($stat === false) {
                            break;
                        }
                    }
                }
            } else {
                $object = new SplFileInfo($path);
                if (
                    static::isExpectedExtension($object, $extensions)
                    && !static::isExcluded($object, $excludeRules)
                ) {
                    $closure($object);
                }
            }
        }
    }

    /**
     * Checking that the extension of the file is in the list of expected extensions
     * @param string $path
     * @param array $extensions
     * @return bool
     */
    public static function isExpectedExtensions(string $path, array $extensions): bool
    {
        $isExpectedExtension = false;
        if (static::isReadable($path) ) {
            $object = new SplFileInfo($path);
            $isExpectedExtension = static::isExpectedExtension($object, $extensions);
        }
        return $isExpectedExtension;
    }

    private static function isExpectedExtension(SplFileInfo $fileInfo, array $extensions): bool
    {
        $ext = $fileInfo->getExtension();
        return !count($extensions) || in_array($ext, $extensions, false);
    }

    /**
     * Copy all files $from -> $to names of what are matched $regExp
     * @param string $from
     * @param string $to
     * @param string $regExp
     * @param $callback
     */
    public static function copy(string $from, string $to, string $regExp = '', $callback = null): void
    {
        self::mapFiles($from, static function (SplFileInfo $fileInfo) use ($regExp, $to, $callback) {
            if (!$regExp || preg_match($regExp, $fileInfo->getFilename())) {

                $sourcePath = $fileInfo->getRealPath();
                // only files allowed
                if ( is_file($sourcePath) && self::isReadable($sourcePath) ) {
                    $newFileName = self::generateFileName($to, $fileInfo->getFilename());
                    $state = copy($sourcePath, $newFileName);
                    if (is_callable($callback)) {
                        $callback($newFileName, $state);
                    }
                }
            }
        });
    }

    public static function generateFileName(string $dir, string $name): string
    {
        $postfix = '';
        do {
            $filePath = rtrim($dir, '/') . '/' . $name;

            if (!empty($postfix)) {
                $filePath .= '_'.$postfix;
            }

            $postfix = (int) $postfix;
            $postfix++;
            $postfix = (string) $postfix;
        } while (file_exists($filePath));
        return $filePath;
    }

    /**
     * @return string
     * @throws CommonException
     */
    public static function getRandomDirPath(): string
    {
        try {
            return sprintf('%02x/%02x', random_int(0, 255), random_int(0, 255));
        } catch (Exception $e) {
            throw new CommonException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
    }
}
