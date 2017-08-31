<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\Http\Controllers\ApiController;
use App\Services\Export\Form1ExportService;
use Illuminate\Http\Request;

class CasesExporterController extends ApiController
{
    public function export(string $form, Request $request)
    {
        $service = null;
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
        ]);
    }
}
