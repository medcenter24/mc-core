<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services;


use App\Services\Import\Dhv24\Dhv24Docx2017Provider;

class CaseImporterService
{
    const DISC_IMPORTS = 'imports';
    const CASES_FOLDERS = 'cases';

    public function import($path)
    {
        // get Docx from the storage and run importer
        $provider = new Dhv24Docx2017Provider();
        $provider->load($path);
        $provider->import();

        return $provider->getLastAccident();
    }
}
