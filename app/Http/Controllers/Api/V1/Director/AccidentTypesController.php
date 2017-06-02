<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\AccidentType;
use App\Http\Controllers\ApiController;
use App\Transformers\AccidentTypeTransformer;

class AccidentTypesController extends ApiController
{
    public function index()
    {
        $types = AccidentType::orderBy('title')->get();
        return $this->response->collection($types, new AccidentTypeTransformer());
    }
}
