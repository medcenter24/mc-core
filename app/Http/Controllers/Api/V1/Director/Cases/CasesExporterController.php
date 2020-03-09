<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2020 (original work) MedCenter24.com;
 */

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases;

use medcenter24\mcCore\App\Exports\CasesExport;
use medcenter24\mcCore\App\Http\Controllers\Api\ApiController;
// use medcenter24\mcCore\App\Services\Export\Form1ExportService;
use Illuminate\Http\Request;

class CasesExporterController extends ApiController
{
    public function export(string $form, Request $request)
    {
        /*$service = null;
        switch ($form) {
            case 'form1':
                $service = new Form1ExportService();
                break;
            default:
                $this->response->errorBadRequest('Undefined form name for the export: '. $form);
        }

        return $service->excel($request->all())->export('xlsx', [
            'Access-Control-Allow-Credentials' => true,
            'Access-Control-Allow-Origin' => env('CORS_ALLOW_ORIGIN_DIRECTOR'),
        ]);*/

        return (new CasesExport())->download('aaa.xlsx');
    }
}
