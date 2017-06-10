<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Doctor;

use App\Diagnostic;
use App\Http\Controllers\ApiController;
use App\Transformers\DiagnosticTransformer;

class DiagnosticsController extends ApiController
{
    public function index()
    {
        $diagnostics = Diagnostic::orderBy('title')->get();
        return $this->response->collection($diagnostics, new DiagnosticTransformer());
    }
}
