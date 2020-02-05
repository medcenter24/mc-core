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
use medcenter24\mcCore\App\Services\Core\Http\Builders\Filter;
use medcenter24\mcCore\App\Services\Core\Http\Builders\Paginator;
use medcenter24\mcCore\App\Services\Core\Http\Builders\RequestBuilder;
use medcenter24\mcCore\App\Services\Core\Http\Builders\Sorter;
use medcenter24\mcCore\App\Services\Core\Http\DataLoaderRequestBuilder;
use medcenter24\mcCore\App\Services\Core\Http\Filter\RequestBuilderFilterTransformer;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;
use \Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{
    use Helpers;
    use ServiceLocatorTrait;

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
    public function callAction($method, $parameters): ?Response
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
     * @param Builder $eloquent
     * @param Request $request
     * @return mixed
     */
    protected function applyCondition(Builder $eloquent, Request $request = null): Builder
    {
        if ($request) {
            // apply filters
            $filters = $request->json('filters');
            if (is_array($filters)
                && array_key_exists('fields', $filters)
                && count($filters['fields']))
            {
                foreach ($filters['fields'] as $key => $filter) {
                    if ($filter['value'] !== null) {
                        $eloquent->where($filter['field'], $this->getFilterAction($filter['match']),
                            $this->getFilterValue($filter));
                    }
                }
            }
        }

        return $eloquent;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws NotImplementedException
     */
    public function search(Request $request): Response
    {
        /** @var DataLoaderRequestBuilder $requestBuilder */
        $requestBuilder = $this->getServiceLocator()->get(DataLoaderRequestBuilder::class);
        $requestBuilder->setPaginator($this->getServiceLocator()->get(Paginator::class)->inject($request->json(DataLoaderRequestBuilder::PAGINATOR)));
        $requestBuilder->setSorter($this->getServiceLocator()->get(Sorter::class)->inject($request->json(DataLoaderRequestBuilder::SORTER)));
        $requestBuilder->setFilter($this->getServiceLocator()->get(Filter::class)->inject($request->json(DataLoaderRequestBuilder::FILTER)));

        /** @var Builder $eloquent */
        $eloquent = call_user_func(array($this->getModelClass(), 'query'));

        $filterTransformer = $this->getServiceLocator()->get(RequestBuilderFilterTransformer::class);
        /** @var Filter $filter */
        foreach ($requestBuilder->getFilter()->getFilters() as $filter) {
            $transformed = $filterTransformer->transform($filter);
            foreach ($transformed as $transform) {

                switch ($transform[Filter::FIELD_MATCH]) {
                    case Filter::MATCH_BETWEEN:
                        $eloquent->whereBetween($transform[Filter::FIELD_NAME], $transform[Filter::FIELD_VALUE]);
                        break;
                    case Filter::MATCH_IN:
                        $eloquent->whereIn($transform[Filter::FIELD_NAME], $transform[Filter::FIELD_VALUE]);
                        break;
                    default:
                        $eloquent->where($transform[Filter::FIELD_NAME], $transform[Filter::FIELD_MATCH],
                            $transform[Filter::FIELD_VALUE], $transform[RequestBuilderFilterTransformer::BOOLEAN]);
                }
            }
        }

        foreach ($requestBuilder->getSorter()->getSortBy() as $sortField) {
            $eloquent->orderBy($sortField[Filter::FIELD_NAME], $sortField[Filter::FIELD_VALUE]);
        }

        // pagination here
        $data = $eloquent->paginate($requestBuilder->getPaginator()->getOffset(), ['*'], 'page', $requestBuilder->getPage());
        return $this->response->paginator($data, $this->getDataTransformer());
    }

    /**
     * Implement models seeker to find data with filters
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     * @throws NotImplementedException
     */
    public function searchOld(Request $request): Response
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
