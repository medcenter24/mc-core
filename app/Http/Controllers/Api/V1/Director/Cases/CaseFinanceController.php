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

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Cases;

use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Http\Controllers\Api\ApiController;
use medcenter24\mcCore\App\Http\Requests\Api\JsonRequest;
use medcenter24\mcCore\App\Models\Formula\Exception\FormulaException;
use medcenter24\mcCore\App\Services\CaseServices\Finance\CaseFinanceService;
use medcenter24\mcCore\App\Services\CaseServices\Finance\CaseFinanceViewService;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Transformers\CaseFinanceTransformer;
use Illuminate\Http\Request;
use Dingo\Api\Http\Response;
use stdClass;
use Throwable;

class CaseFinanceController extends ApiController
{
    /**
     * @param Request $request
     * @param int $id
     * @param CaseFinanceViewService $caseFinanceViewService
     * @param AccidentService $accidentService
     * @return Response
     * @throws FormulaException
     * @throws InconsistentDataException
     * @throws Throwable
     */
    public function show(
        Request $request,
        int $id,
        CaseFinanceViewService $caseFinanceViewService,
        AccidentService $accidentService
    ): Response
    {
        /** @var Accident $accident */
        $accident = $accidentService->first([AccidentService::FIELD_ID => $id]);
        if (!$accident) {
            $this->response->errorNotFound();
        }
        $sTypes = $request->get('types', '');
        $types = $sTypes ? explode(',', $sTypes) : CaseFinanceViewService::FINANCE_TYPES;
        return $this->getResponse($types, $accident, $caseFinanceViewService);
    }

    /**
     * @param int $id
     * @param string $type
     * @param CaseFinanceService $caseFinanceService
     * @param JsonRequest $request
     * @param CaseFinanceViewService $caseFinanceViewService
     * @param AccidentService $accidentService
     * @return Response
     * @throws FormulaException
     * @throws InconsistentDataException
     * @throws Throwable
     */
    public function save(
        int $id,
        string $type,
        CaseFinanceService $caseFinanceService,
        JsonRequest $request,
        CaseFinanceViewService $caseFinanceViewService,
        AccidentService $accidentService
    ): Response
    {
        /** @var Accident $accident */
        $accident = $accidentService->first([AccidentService::FIELD_ID => $id]);
        if (!$accident) {
            $this->response->errorNotFound();
        }
        $jsonData = $request->json() ? $request->json()->all() : [];

        $caseFinanceService->save(
            $accident,
            $type,
            $jsonData);

        return $this->getResponse(
            CaseFinanceViewService::FINANCE_TYPES,
            $accident,
            $caseFinanceViewService
        );
    }

    /**
     * @param $types
     * @param Accident $accident
     * @param CaseFinanceViewService $caseFinanceViewService
     * @return Response
     * @throws InconsistentDataException
     * @throws FormulaException
     * @throws Throwable
     */
    private function getResponse(
        $types,
        Accident $accident,
        CaseFinanceViewService $caseFinanceViewService
    ): Response
    {
        $obj = new stdClass();
        $obj->collection = $caseFinanceViewService->get($accident, $types);
        return $this->response->item($obj, new CaseFinanceTransformer());
    }
}
