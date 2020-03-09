<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2020 (original work) MedCenter24.com;
 */

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Http\Controllers\Api;

use Dingo\Api\Exception\ValidationHttpException;
use Dingo\Api\Http\Request;
use Dingo\Api\Http\Response;
use Illuminate\Support\Facades\URL;
use League\Fractal\TransformerAbstract;
use medcenter24\mcCore\App\Contract\General\Service\ModelService;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Exceptions\NotImplementedException;
use medcenter24\mcCore\App\Http\Requests\Api\JsonRequest;
use medcenter24\mcCore\App\Services\ApiSearch\ApiSearchService;
use medcenter24\mcCore\App\Services\ApiSearch\SearchFieldLogic;

/**
 * Provides eloquent models
 * Class ModelApiController
 * @package medcenter24\mcCore\App\Http\Controllers\Api
 */
abstract class ModelApiController extends ApiController
{
    /**
     * Complex search with relations
     * @return SearchFieldLogic
     */
    protected function searchFieldLogic(): ?SearchFieldLogic
    {
        return null; // will be used default SearchFieldLogic
    }

    /**
     * @return TransformerAbstract
     * @throws NotImplementedException
     */
    abstract protected function getDataTransformer(): TransformerAbstract;

    /**
     * Service to manage current model data
     * @return ModelService
     */
    abstract protected function getModelService(): ModelService;

    /**
     * this request will be generated from globals
     * @return string
     */
    protected function getRequestClass(): string
    {
        return JsonRequest::class;
    }

    private function urlToTheSource(int $id): string
    {
        return URL::action('\\' . static::class . '@show', ['id' => $id]);
    }

    /////// RestAPI functions
    ///
    /**
     * @param Request $request
     * @return Response
     * @throws NotImplementedException
     */
    public function search(Request $request): Response
    {
        /** @var ApiSearchService $searchService */
        $searchService = $this->getServiceLocator()->get(ApiSearchService::class);
        $searchService->setFieldLogic($this->searchFieldLogic());

        $data = $searchService->search($request, $this->getModelService()->getModel());
        // fix it and add sort filtering and other if needed, for now it []
        $data->withPath($request->path());
        return $this->response->paginator($data, $this->getDataTransformer());
    }

    /**
     * @param int $id
     * @return Response
     * @throws NotImplementedException
     */
    public function show(int $id): Response
    {
        $model = $this->getModelService()->first(['id' => $id]);
        if (!$model) {
            $this->response->errorNotFound();
        }
        return $this->response->item($model, $this->getDataTransformer());
    }

    /**
     * Create
     * @return Response|null
     */
    public function store(JsonRequest $request): Response
    {
        /** @var JsonRequest $request */
        $request = call_user_func([$this->getRequestClass(), 'createFromBase'], $request);
        $request->validate();

        try {
            $data = $this->getDataTransformer()->inverseTransform($request->all());
            $model = $this->getModelService()->create($data);

            return $this->response->created(
                $this->urlToTheSource($model->getAttribute('id')),
                $this->getDataTransformer()->transform($model)
            );
        } catch(InconsistentDataException $e) {
            throw new ValidationHttpException([$e->getMessage()]);
        } catch (NotImplementedException $e) {
            $this->response->errorInternal();
        }
        return null;
    }

    /**
     * Update model
     * @param int $id
     * @param JsonRequest $request
     * @return Response|null
     */
    public function update(int $id, JsonRequest $request): Response
    {
        /** @var JsonRequest $request */
        $request = call_user_func([$this->getRequestClass(), 'createFromBase'], $request);
        $request->validate();

        try {
            $data = $this->getDataTransformer()->inverseTransform($request->all());
            $data['id'] = $id;
            $model = $this->getModelService()->findAndUpdate(['id'], $data);
            return $this->response->accepted(
                $this->urlToTheSource($id),
                $this->getDataTransformer()->transform($model));
        } catch (NotImplementedException $e) {
            throw new ValidationHttpException([$e->getMessage()]);
        } catch (InconsistentDataException $e) {
            // it can be here
            throw new ValidationHttpException([$e->getMessage()]);
        }
    }

    /**
     * Delete model
     * @param $id
     * @return Response
     */
    public function destroy($id): Response
    {
        try {
            $this->getModelService()->delete($id);
        } catch (InconsistentDataException $e) {
            $this->response->errorNotFound();
        }
        return $this->response->noContent();
    }
}
