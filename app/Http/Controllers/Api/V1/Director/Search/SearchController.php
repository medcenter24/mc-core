<?php
/*
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
 * Copyright (c) 2022 (original work) MedCenter24.com;
 */

declare(strict_types=1);

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Search;

use Dingo\Api\Http\Request;
use Dingo\Api\Http\Response;
use medcenter24\mcCore\App\Http\Controllers\Api\ApiController;
use medcenter24\mcCore\App\Services\Search\SearchRequest;
use medcenter24\mcCore\App\Services\Search\SearchResultService;
use medcenter24\mcCore\App\Services\Search\SearchService;
use Symfony\Component\HttpFoundation\ParameterBag;

class SearchController extends ApiController
{
    public function search(
        Request $request,
        SearchRequest $searchRequest,
        SearchService $searchService,
        SearchResultService $searchResultService,
    ): Response {
        /** @var ParameterBag $searchRequestParameterBag */
        $searchRequestParameterBag = $request->json();
        $searchRequest->load($searchRequestParameterBag);
        $data = $searchService->search($searchRequest);

        if ($searchRequest->getResultType() === 'excel') {
            var_dump('excel data loader');die;
        }

        return $this->response()->array($data->toArray());
    }
}
