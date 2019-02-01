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
use Dingo\Api\Http\Response;
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

    protected function getModelClass(): string
    {
        return FinanceCondition::class;
    }

    /**
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function show($id): Response
    {
        $finance = FinanceCondition::findOrFail($id);
        return $this->response->item($finance, new FinanceConditionTransformer());
    }

    /**
     * Add new rule
     * @param FinanceRequest $request
     * @param CaseFinanceService $caseFinanceService
     * @return \Dingo\Api\Http\Response
     * @throws \App\Exceptions\InconsistentDataException
     */
    public function store(FinanceRequest $request, CaseFinanceService $caseFinanceService): Response
    {
        $financeCondition = $caseFinanceService->updateFinanceConditionByRequest($request);
        $transformer = new FinanceConditionTransformer();
        return $this->response->created(url("pages/settings/finance/{$financeCondition->id}"), $transformer->transform($financeCondition));
    }

    /**
     * Update existing rule
     * @param $id
     * @param FinanceRequest $request
     * @param CaseFinanceService $caseFinanceService
     * @return \Dingo\Api\Http\Response
     * @throws \App\Exceptions\InconsistentDataException
     */
    public function update($id, FinanceRequest $request, CaseFinanceService $caseFinanceService)
    {
        $financeCondition = $caseFinanceService->updateFinanceConditionByRequest($request, $id);
        return $this->response->item($financeCondition, new FinanceConditionTransformer());
    }

    /**
     * Destroy rule
     * Neither Real finance condition or finance rules from the finance_storage will be deleted
     *   for the support and backward data compatibility
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function destroy($id) {
        $condition = FinanceCondition::findOrFail($id);
        \Log::info('Finance condition deleted', [$condition, $this->user()]);
        $condition->delete();
        return $this->response->noContent();
    }
}
