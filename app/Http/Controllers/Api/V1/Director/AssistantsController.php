<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\Assistant;
use App\Http\Controllers\ApiController;
use App\Transformers\AssistantTransformer;
use App\Transformers\ModelTransformer;

class AssistantsController extends ApiController
{
    public function index()
    {
        return $this->response->collection(
            Assistant::orderBy('title')->get(),
            new AssistantTransformer()
        );
    }
}
