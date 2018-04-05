<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\Assistant;
use App\City;
use App\Doctor;
use App\DoctorService;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\FinanceRequest;
use App\Services\CaseServices\CaseFinanceService;
use App\Transformers\FinanceConditionTransformer;

class FinanceController extends ApiController
{
    /**
     * List of rules
     */
    public function index() {}

    public function show($id) {}

    /**
     * Add new rule
     * @param FinanceRequest $request
     * @param CaseFinanceService $caseFinanceService
     * @return \Dingo\Api\Http\Response
     */
    public function store(FinanceRequest $request, CaseFinanceService $caseFinanceService)
    {
        $financeCondition = $caseFinanceService->updateFinanceConditionByRequest($request);
        return $this->response->created(action('Api\V1\Director\FinanceController@show', [$financeCondition]),
            $financeCondition->toArray());
    }

    /**
     * Update existing rule
     * @param $id
     * @param FinanceRequest $request
     * @param CaseFinanceService $caseFinanceService
     * @return \Dingo\Api\Http\Response
     */
    public function update($id, FinanceRequest $request, CaseFinanceService $caseFinanceService)
    {
        $financeCondition = $caseFinanceService->updateFinanceConditionByRequest($request, $id);
        return $this->response->item($financeCondition, new FinanceConditionTransformer());
    }

    /**
     * Destroy rule
     */
    public function destroy($id) {}
}
