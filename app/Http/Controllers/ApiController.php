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
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use League\Fractal\TransformerAbstract;
use \Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{
    use Helpers;

    public function __construct()
    {
        parent::__construct();
        Auth::setDefaultDriver('api');
    }

    /**
     * @param string $method
     * @param array $parameters
     * @return Response
     */
    public function callAction($method, $parameters): Response
    {
        try {
            return parent::callAction($method, $parameters);
        } catch (ModelNotFoundException $e) {
            Log::debug($e->getMessage());
            $this->response->error('Not found', 404);
        }
    }

    /**
     * To have possibility to add some conditions
     * # notice: do not want to search by all of the visible properties because we need to control that
     * # we need to control filter's types and not all filters are able to be searchable
     * @param $eloquent
     * @param Request $request
     * @return mixed
     */
    protected function applyCondition($eloquent, Request $request = null)
    {
        return $eloquent;
    }

    /**
     * Implement models seeker to find data with filters
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     * @throws NotImplementedException
     */
    public function search(Request $request)
    {
        // first
        $first = (int)$request->json('first', false);
        // 3000 like a all but not to overload server
        $rows = (int)$request->json('rows', 3000);
        if ($first !== false) {
            $page = ($first / $rows) + 1;
        } else {
            $page = (int)$request->json('page', 0);
        }

        $sortField = $this->getSortField($request->json('sortField', 'id'));
        $sortField = $sortField ?: 'id';

        $sortOrder = $request->json('sortOrder', 1) > 0 ? 'asc' : 'desc';

        $eloquent = call_user_func(array($this->getModelClass(), 'orderBy'), $sortField, $sortOrder);
        $eloquent = $this->applyCondition($eloquent, $request);

        // default conditions for all models
        $ids = $request->json('ids', []);
        if (count($ids)) {
            $eloquent->whereIn('id', $ids);
        }

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
