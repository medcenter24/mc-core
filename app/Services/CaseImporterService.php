<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services;


use App\Services\Import\Dhv24\Dhv24Docx2017Provider;
use App\Upload;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Storage;

class CaseImporterService
{
    const DISC_IMPORTS = 'imports';
    const CASES_FOLDERS = 'cases';

    public function upload($file)
    {
        return new Upload([
            'path' => Storage::disk(self::DISC_IMPORTS)->putFile(self::CASES_FOLDERS, $file),
            'file_name' => $file->name,
        ]);
    }

    public function import($uploadId)
    {
        $upload = Upload::findOrFail($uploadId);
        $path = Storage::disk(self::DISC_IMPORTS)->get($upload->path);

        // get Docx from the storage and run importer
        $provider = new Dhv24Docx2017Provider();
        $provider->load($path);
        $provider->import();
        return $provider->getLastAccident();
    }
}
