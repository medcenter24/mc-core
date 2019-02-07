<?php
/**
 * Copyright (c) 2019.
 *
 * Oleksandr Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director\Cases;


use App\Accident;
use App\Http\Controllers\ApiController;
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
    public function show(
        Request $request,
        int $id,
        CaseFinanceViewService $caseFinanceViewService
    ): Response
    {
        /** @var Accident $accident */
        $accident = Accident::findOrFail($id);
        $types = $request->json('types', CaseFinanceViewService::FINANCE_TYPES);
        $obj = new \stdClass();
        $obj->collection = $caseFinanceViewService->getAccidentFinance($accident, $types);;
        return $this->response->item($obj, new CaseFinanceTransformer());
    }
}
