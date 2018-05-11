<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers;

use App\Exceptions\NotImplementedException;
use Dingo\Api\Routing\Helpers;
use Illuminate\Database\Eloquent\Model;
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
     * To have possibility to add some conditions
     * @param $eloquent
     * @return mixed
     */
    protected function applyCondition($eloquent)
    {
        return $eloquent;
    }

    /**
     * Implement models seeker to find data with filters
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     * @throws NotImplementedException
     * @throws \ErrorException
     */
    public function search(Request $request)
    {
        $first = $request->json('first', false);
        $rows = $request->json('rows', 25);
        if ($first !== false) {
            $rows = $rows ? $rows : 25;
            $page = ($first / $rows) + 1;
        } else {
            $page = $request->json('page', 0);
        }

        $sortField = $this->getSortField($request->json('sortField', 'id'));
        $sortField = $sortField ?: 'id';

        $sortOrder = $request->json('sortOrder', 1) > 0 ? 'asc' : 'desc';

        $eloquent = call_user_func(array($this->getModelClass(), 'orderBy'), $sortField, $sortOrder);
        $eloquent = $this->applyCondition($eloquent);
        $data = $eloquent->paginate($rows, ['*'], 'page', $page);
        return $this->response->paginator($data, $this->getDataTransformer());
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

    /**
     * @param $fieldName
     * @return string
     * @throws NotImplementedException
     */
    private function getSortField($fieldName)
    {
        $field = '';
        $fields = [];

        $class = $this->getModelClass();
        if (class_exists($class)) {
            /** @var Model $model */
            $model = new $class;
            $fields = $model->getVisible();
            $field = camel_case($fieldName);
        }
        return in_array($field, $fields) ? $field : 'id';
    }
}
