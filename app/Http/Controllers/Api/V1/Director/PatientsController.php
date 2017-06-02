<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\Http\Controllers\ApiController;
use App\Patient;
use App\Transformers\PatientTransformer;

class PatientsController extends ApiController
{
    public function show($id)
    {
        return $this->response->item(
            Patient::findOrFail($id),
            new PatientTransformer()
        );
    }
}
