<?php
/**
 * Copyright (c) 2019.
 *
 * Oleksandr Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director\Cases;


use App\Accident;
use App\Http\Controllers\ApiController;
use App\Services\CaseServices\Finance\CaseFinanceService;
use App\Services\CaseServices\Finance\CaseFinanceViewService;
use App\Transformers\CaseFinanceTransformer;
use Illuminate\Http\Request;
use Dingo\Api\Http\Response;

class CaseFinanceController extends ApiController
{
    /**
     * @param Request $request
     * @param int $id
     * @param CaseFinanceViewService $caseFinanceViewService
     * @return Response
     * @throws \App\Exceptions\InconsistentDataException
     * @throws \App\Models\Formula\Exception\FormulaException
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
     * @throws \App\Exceptions\InconsistentDataException
     * @throws \App\Models\Formula\Exception\FormulaException
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
     * @throws \App\Exceptions\InconsistentDataException
     * @throws \App\Models\Formula\Exception\FormulaException
     * @throws \Throwable
     */
    private function getResponse($types, Accident $accident, CaseFinanceViewService $caseFinanceViewService): Response
    {
        $obj = new \stdClass();
        $obj->collection = $caseFinanceViewService->get($accident, $types);
        return $this->response->item($obj, new CaseFinanceTransformer());
    }
}
