<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Doctor;

use App\Accident;
use App\Diagnostic;
use App\Http\Controllers\ApiController;
use App\Transformers\DiagnosticTransformer;

class DiagnosticsController extends ApiController
{
    /**
     * To make it easy and more usable we don't need diagnostics which were created by doctor
     * because they don't have all data and director should check all these cases
     * @return \Dingo\Api\Http\Response
     */
    public function index()
    {
        $diagnostics = Diagnostic::join('diagnosticables', 'diagnosticables.diagnostic_id', '=', 'diagnostics.id')
            ->where('diagnosticables.diagnosticable_type', '=', Accident::class)
            ->orderBy('diagnostics.title')
            ->get();
        return $this->response->collection($diagnostics, new DiagnosticTransformer());
    }
}
