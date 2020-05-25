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

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use medcenter24\mcCore\App\Exports\CasesExport;
use medcenter24\mcCore\App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExporterController extends ApiController
{
    /**
     * @param string $form
     * @param Request $request
     * @return Response|BinaryFileResponse|void
     */
    public function export(string $form, Request $request)
    {
        /*$service = null;
        return $service->excel($request->all())->export('xlsx', [
            'Access-Control-Allow-Credentials' => true,
            'Access-Control-Allow-Origin' => env('CORS_ALLOW_ORIGIN_DIRECTOR'),
        ]);*/

        switch ($form) {
            case 'cases':
                return (new CasesExport())->download(time() . '_case_export.xlsx');
        }

        $this->response->errorNotFound();
    }
}
