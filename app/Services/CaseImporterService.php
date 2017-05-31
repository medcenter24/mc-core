<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services;


use App\Upload;
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
}
