<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services;


use App\Services\Import\Dhv24\Dhv24Docx2017Provider;
use App\Upload;
use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Storage;

class CaseImporterService
{
    const DISC_IMPORTS = 'imports';
    const CASES_FOLDERS = 'cases';

    public function upload(UploadedFile $file)
    {
        return new Upload([
            'path' => Storage::disk(self::DISC_IMPORTS)->putFile(self::CASES_FOLDERS, $file),
            'file_name' => $file->getClientOriginalName(),
        ]);
    }

    public function import($uploadId)
    {
        $upload = Upload::findOrFail($uploadId);
        $path = storage_path(self::DISC_IMPORTS . DIRECTORY_SEPARATOR . $upload->path);

        // get Docx from the storage and run importer
        $provider = new Dhv24Docx2017Provider();
        $provider->load($path);
        $provider->import();
        $this->delete($uploadId);
        return $provider->getLastAccident();
    }

    public function getUploadedCases(User $user)
    {
        return $user->uploadedCases()->get();
    }

    public function delete($id)
    {
        $upload = Upload::findOrFail($id);
        Storage::disk(self::DISC_IMPORTS)->delete($upload->path);
        $upload->forceDelete();
    }
}
