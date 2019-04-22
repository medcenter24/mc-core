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

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director;

use medcenter24\mcCore\App\FinanceCondition;
use medcenter24\mcCore\App\Http\Controllers\ApiController;
use medcenter24\mcCore\App\Http\Requests\Api\FinanceRequest;
use medcenter24\mcCore\App\Services\CaseServices\Finance\CaseFinanceService;
use medcenter24\mcCore\App\Transformers\FinanceConditionTransformer;
use Dingo\Api\Http\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use League\Fractal\TransformerAbstract;

class FinanceController extends ApiController
{
    protected function applyCondition($eloquent, Request $request = null): Builder
    {
        $id = (int)$request->json('id', false);
        if ($id) {
            $eloquent->where('id', $id);
        }
        return $eloquent;
    }

    protected function getDataTransformer(): TransformerAbstract
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
     */
    public function update($id, FinanceRequest $request, CaseFinanceService $caseFinanceService): Response
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
