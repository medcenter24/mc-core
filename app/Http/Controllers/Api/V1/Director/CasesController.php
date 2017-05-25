<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\Accident;
use App\Transformers\CasesTransformer;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CasesController extends Controller
{
    use Helpers;

    public function index()
    {
        $accidents = Accident::paginate(5);
        return $this->response->paginator($accidents, new CasesTransformer);
    }
}
