<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services;

use App\Support\Core\Configurable;
use App\Upload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;


/**
 * Media storage for storing images which were uploaded but still haven't been bound to the any model
 * Class MediaService
 * @package App\Services
 */
class UploaderService extends Configurable
{
    const CONF_DISK = 'disk';
    const CONF_FOLDER = 'folder';

    private $_defaults = [
        self::CONF_DISK => 'media',
        self::CONF_FOLDER => 'anonymous',
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

    public function upload(UploadedFile $file)
    {
        return new Upload([
            'path' => Storage::disk($this->getOption(self::CONF_DISK))->putFile($this->getOption(self::CONF_FOLDER), $file),
            'file_name' => $file->getClientOriginalName(),
            'storage' => $this->getOption(self::CONF_FOLDER),
        ]);
    }

    public function getPathById($uploadId = 0)
    {
        $upload = Upload::findOrFail($uploadId);
        return storage_path($this->getOption(self::CONF_DISK) . DIRECTORY_SEPARATOR . $upload->path);
    }

    public function delete($uploadId)
    {
        $upload = Upload::findOrFail($uploadId);
        Storage::disk($this->getOption(self::CONF_DISK))->delete($upload->path);
        $upload->forceDelete();
    }
}
