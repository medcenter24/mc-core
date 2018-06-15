<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\FinanceCondition;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\FinanceRequest;
use App\Services\CaseServices\CaseFinanceService;
use App\Transformers\FinanceConditionTransformer;
use Illuminate\Http\Request;

class FinanceController extends ApiController
{
    protected function applyCondition($eloquent, Request $request = null)
    {
        $id = (int)$request->json('id', false);
        if ($id) {
            $eloquent->where('id', $id);
        }
        return $eloquent;
    }

    protected function getDataTransformer()
    {
        return new FinanceConditionTransformer();
    }

    protected function getModelClass()
    {
        return FinanceCondition::class;
    }

    /**
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function show($id)
    {
        $finance = FinanceCondition::findOrFail($id);
        return $this->response->item($finance, new FinanceConditionTransformer());
    }

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
