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

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases;

use Dingo\Api\Http\Request;
use Illuminate\Support\Facades\App;
use League\Fractal\TransformerAbstract;
use medcenter24\mcCore\App\Exports\CasesExport;
use medcenter24\mcCore\App\Services\ApiSearch\ApiSearchService;
use medcenter24\mcCore\App\Transformers\CaseExportTransformer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CaseExporterController extends CaseAccidentController
{
    /**
     * @inheritDoc
     */
    protected function getDataTransformer(): TransformerAbstract
    {
        return new CaseExportTransformer();
    }

    public function export(Request $request): BinaryFileResponse
    {
        // change lang for exported data (as export builds on the back end only)
        $lang = $request->json('lang', 'en');
        App::setLocale($lang);

        /** @var ApiSearchService $searchService */
        $searchService = $this->getServiceLocator()->get(ApiSearchService::class);
        $searchService->setFieldLogic($this->searchFieldLogic());

        $data = $searchService->getCollection($request, $this->getModelService()->getModel());
        return (new CasesExport($data))->download(time() . '_cases_export.xlsx');
    }
}
