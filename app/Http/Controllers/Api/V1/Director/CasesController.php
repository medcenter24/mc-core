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
use Illuminate\Support\Facades\DB;

class CasesController extends Controller
{
    use Helpers;

    public function index(Request $request)
    {
        $rows = $request->get('rows', 10);
        $accidents = Accident::paginate($rows, $columns = ['*'], $pageName = 'page', $request->get('page', null)+1);

        return $this->response->paginator($accidents, new CasesTransformer);
    }
}
