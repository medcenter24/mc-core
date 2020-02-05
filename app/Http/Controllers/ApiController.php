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

use medcenter24\mcCore\App\Exceptions\NotImplementedException;
use Dingo\Api\Routing\Helpers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use League\Fractal\TransformerAbstract;
use medcenter24\mcCore\App\Services\Core\Http\Builders\Filter;
use medcenter24\mcCore\App\Services\Core\Http\Builders\Paginator;
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
     * Internal transformer for the current model search
     * @param $eloquent
     * @param array $filters
     * @return array
     */
    protected function searchTransformer(Builder $eloquent, array $filters): array {
        return $filters;
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
        /** @var Paginator $paginator */
        $paginator = $this->getServiceLocator()->get(Paginator::class)->create();
        $paginator->inject($request->json(DataLoaderRequestBuilder::PAGINATOR, []));
        $requestBuilder->setPaginator($paginator);
        /** @var Sorter $sorter */
        $sorter = $this->getServiceLocator()->get(Sorter::class)->create();
        $sorter->inject($request->json(DataLoaderRequestBuilder::SORTER, []));
        $requestBuilder->setSorter($sorter);
        /** @var Filter $filter */
        $filter = $this->getServiceLocator()->get(Filter::class)->create();
        $filter->inject($request->json(DataLoaderRequestBuilder::FILTER, []));
        $requestBuilder->setFilter($filter);

        /** @var Builder $eloquent */
        $eloquent = call_user_func(array($this->getModelClass(), 'query'));

        /** @var RequestBuilderFilterTransformer $filterTransformer */
        $filterTransformer = $this->getServiceLocator()->get(RequestBuilderFilterTransformer::class);
        /** @var array $filter */
        foreach ($requestBuilder->getFilter()->getFilters() as $filter) {
            // general transformer
            $transformed = $filterTransformer->transform($filter);
            // internal transformer
            $transformed = $this->searchTransformer($eloquent, $transformed);
            foreach ($transformed as $transform) {
                switch ($transform[Filter::FIELD_MATCH]) {
                    case Filter::MATCH_BETWEEN:
                        $eloquent->whereBetween($transform[Filter::FIELD_NAME], $transform[Filter::FIELD_VALUE]);
                        break;
                    case Filter::MATCH_IN:
                        $eloquent->whereIn($transform[Filter::FIELD_NAME], $transform[Filter::FIELD_VALUE]);
                        break;
                    case 'ilike':
                        $eloquent->whereRaw('UPPER('
                            . $transform[Filter::FIELD_NAME]
                            . ") LIKE '" .mb_strtoupper($transform[Filter::FIELD_VALUE])."'");
                        break;
                    default:
                        $eloquent->where($transform[Filter::FIELD_NAME], $transform[Filter::FIELD_MATCH],
                            $transform[Filter::FIELD_VALUE]);
                }
            }
        }

        foreach ($requestBuilder->getSorter()->getSortBy() as $sortField) {
            $eloquent->orderBy($sortField[Filter::FIELD_NAME], $sortField[Filter::FIELD_VALUE]);
        }

        // debug
        Log::info($eloquent->toSql(), [$eloquent]);

        // pagination here
        $data = $eloquent->paginate($requestBuilder->getPaginator()->getOffset(), ['*'], 'page', $requestBuilder->getPage());
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
}
