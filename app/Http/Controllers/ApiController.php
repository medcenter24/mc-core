<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

namespace medcenter24\mcCore\App\Http\Controllers;

use Illuminate\Support\Str;
use medcenter24\mcCore\App\Exceptions\NotImplementedException;
use Dingo\Api\Routing\Helpers;
use Illuminate\Database\Eloquent\Builder;
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
    protected function applyCondition($eloquent, Request $request = null): Builder
    {
        if ($request) {
            // apply filters
            $filters = $request->json('filters');
            if (is_array($filters) && count($filters)) {
                foreach ($filters as $field => $filter) {
                    $eloquent->where($field, $this->getAction($filter['matchMode']), $filter['value']);
                }
            }
        }

        return $eloquent;
    }

    private function getAction(string $act): string {
        switch ($act) {
            case 'eq':
            default:
                $action = '=';
        }
        return $action;
    }

    /**
     * Implement models seeker to find data with filters
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     * @throws NotImplementedException
     */
    public function search(Request $request): Response
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
    protected function getModelClass(): string {
        throw new NotImplementedException('ApiController::getModelClass needs to be rewrote by the child');
    }

    /**
     * @return TransformerAbstract
     * @throws NotImplementedException
     */
    protected function getDataTransformer(): TransformerAbstract {
        throw new NotImplementedException('ApiController::getDataTransformer needs to be rewrote by the child');
    }

    /**
     * @param $fieldName
     * @return string
     * @throws NotImplementedException
     */
    private function getSortField($fieldName): string
    {
        $field = '';
        $fields = [];

        $class = $this->getModelClass();
        if (class_exists($class)) {
            /** @var Model $model */
            $model = new $class;
            $fields = $model->getVisible();
            $field = Str::camel($fieldName);
        }
        return in_array($field, $fields) ? $field : 'id';
    }
}
