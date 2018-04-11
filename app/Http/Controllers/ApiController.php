<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers;

use App\Exceptions\NotImplementedException;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class ApiController extends Controller
{
    use Helpers;

    public function __construct()
    {
        parent::__construct();
        Auth::setDefaultDriver('api');
    }

    /**
     * Implement datatable models to the controllers
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     * @throws NotImplementedException
     */
    public function datatable(Request $request)
    {
        $first = $request->json('first', false);
        $rows = $request->json('rows', 25);
        if ($first !== false) {
            $rows = $rows ? $rows : 25;
            $page = ($first / $rows) + 1;
        } else {
            $page = $request->json('page', 0);
        }

        $sortField = $request->json('sortField', 'title');
        $sortField = $sortField ?: 'title';

        $sortOrder = $request->json('sortOrder', 1) > 0 ? 'asc' : 'desc';

        $eloquent = call_user_func(array($this->getModelClass(), 'orderBy'), $sortField, $sortOrder);
        $datePeriods = $eloquent->paginate($rows, ['*'], 'page', $page);

        return $this->response->paginator($datePeriods, $this->getDataTransformer());
    }

    /**
     * @return string Class with Eloquent Model
     * @throws NotImplementedException
     */
    protected function getModelClass() {
        throw new NotImplementedException('ApiController::getModelClass needs to be rewrote by the child');
    }

    /**
     * @return TransformerAbstract
     * @throws NotImplementedException
     */
    protected function getDataTransformer() {
        throw new NotImplementedException('ApiController::getDataTransformer needs to be rewrote by the child');
    }
}
