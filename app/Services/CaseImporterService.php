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
