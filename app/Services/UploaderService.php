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

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Services;

use medcenter24\mcCore\App\Support\Core\Configurable;
use medcenter24\mcCore\App\Upload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Media storage for storing images which were uploaded but still haven't been bound to the any model
 * Class MediaService
 * @package medcenter24\mcCore\App\Services
 */
class UploaderService extends Configurable
{
    public const CONF_DISK = 'disk';
    public const CONF_FOLDER = 'folder';

    public const CONF_DEFAULT = 'uploads';

    private $_defaults = [
        self::CONF_DISK => self::CONF_DEFAULT,
        self::CONF_FOLDER => self::CONF_DEFAULT,
    ];

    /**
     * public constructor to allow the object to be recreated from php code
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        $options = array_merge($this->_defaults, $options);
        parent::__construct($options);
    }

    public function upload(UploadedFile $file): Upload
    {
        return new Upload([
            'path' => Storage::disk($this->getOption(self::CONF_DISK))->putFile($this->randDir($this->getOption(self::CONF_FOLDER)), $file),
            'file_name' => $file->getClientOriginalName(),
            'storage' => $this->getOption(self::CONF_FOLDER),
        ]);
    }

    public function getPathById($uploadId = 0): string
    {
        $upload = Upload::findOrFail($uploadId);
        return storage_path($this->getOption(self::CONF_DISK) . DIRECTORY_SEPARATOR . $upload->path);
    }

    public function delete($uploadId): void
    {
        $upload = Upload::findOrFail($uploadId);
        Storage::disk($this->getOption(self::CONF_DISK))->delete($upload->path);
        $upload->forceDelete();
    }

    /**
     * Generates directory structures
     * @param $rootFolderName
     * @return string
     */
    private function randDir($rootFolderName): string
    {
        $nested = sprintf("%02x" . DIRECTORY_SEPARATOR . "%02x", mt_rand(0, 255), mt_rand(0, 255));
        return $rootFolderName . DIRECTORY_SEPARATOR . $nested;
    }
}
