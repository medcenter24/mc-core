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

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Http\Controllers;

use medcenter24\mcCore\App\Exceptions\NotImplementedException;
use Dingo\Api\Routing\Helpers;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use League\Fractal\TransformerAbstract;
use medcenter24\mcCore\App\Services\ApiSearch\ApiSearchService;
use medcenter24\mcCore\App\Services\ApiSearch\SearchFieldLogic;
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
        return null;
    }

    /**
     * Complex search with relations
     * @return SearchFieldLogic
     */
    protected function searchFieldLogic(): ?SearchFieldLogic
    {
        return null; // will be used default SearchFieldLogic
    }

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
        $data = $searchService->search($request, $this->getModelClass());
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
