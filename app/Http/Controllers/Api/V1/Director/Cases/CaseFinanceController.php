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

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases;


use medcenter24\mcCore\App\Accident;
use medcenter24\mcCore\App\Http\Controllers\ApiController;
use medcenter24\mcCore\App\Services\CaseServices\Finance\CaseFinanceService;
use medcenter24\mcCore\App\Services\CaseServices\Finance\CaseFinanceViewService;
use medcenter24\mcCore\App\Transformers\CaseFinanceTransformer;
use Illuminate\Http\Request;
use Dingo\Api\Http\Response;

class CaseFinanceController extends ApiController
{
    /**
     * @param Request $request
     * @param int $id
     * @param CaseFinanceViewService $caseFinanceViewService
     * @return Response
     * @throws \medcenter24/mcCore\Exceptions\InconsistentDataException
     * @throws \medcenter24/mcCore\Models\Formula\Exception\FormulaException
     * @throws \Throwable
     */
    public function show(Request $request, int $id, CaseFinanceViewService $caseFinanceViewService): Response
    {
        /** @var Accident $accident */
        $accident = Accident::findOrFail($id);
        $types = $request->json('types', CaseFinanceViewService::FINANCE_TYPES);
        if (!count($types)) {
            $types = CaseFinanceViewService::FINANCE_TYPES;
        }
        return $this->getResponse($types, $accident, $caseFinanceViewService);
    }

    /**
     * @param int $id
     * @param string $type
     * @param CaseFinanceService $caseFinanceService
     * @param Request $request
     * @param CaseFinanceViewService $caseFinanceViewService
     * @return Response
     * @throws \medcenter24/mcCore\Exceptions\InconsistentDataException
     * @throws \medcenter24/mcCore\Models\Formula\Exception\FormulaException
     * @throws \Throwable
     */
    public function save(
        int $id,
        string $type,
        CaseFinanceService $caseFinanceService,
        Request $request,
        CaseFinanceViewService $caseFinanceViewService): Response
    {
        /** @var Accident $accident */
        $accident = Accident::findOrFail($id);
        $caseFinanceService->save($accident, $type, json_decode($request->getContent(), 1));
        return $this->getResponse(CaseFinanceViewService::FINANCE_TYPES, $accident, $caseFinanceViewService);
    }

    /**
     * @param $types
     * @param Accident $accident
     * @param CaseFinanceViewService $caseFinanceViewService
     * @return Response
     * @throws \medcenter24/mcCore\Exceptions\InconsistentDataException
     * @throws \medcenter24/mcCore\Models\Formula\Exception\FormulaException
     * @throws \Throwable
     */
    private function getResponse($types, Accident $accident, CaseFinanceViewService $caseFinanceViewService): Response
    {
        $obj = new \stdClass();
        $obj->collection = $caseFinanceViewService->get($accident, $types);
        return $this->response->item($obj, new CaseFinanceTransformer());
    }
}
