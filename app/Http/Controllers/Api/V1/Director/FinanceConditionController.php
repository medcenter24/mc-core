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

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director;

use Dingo\Api\Http\Response;
use JetBrains\PhpStorm\Pure;
use medcenter24\mcCore\App\Contract\General\Service\ModelService;
use medcenter24\mcCore\App\Exceptions\NotImplementedException;
use medcenter24\mcCore\App\Http\Controllers\Api\ModelApiController;
use medcenter24\mcCore\App\Http\Requests\Api\FinanceConditionRequest;
use medcenter24\mcCore\App\Http\Requests\Api\JsonRequest;
use medcenter24\mcCore\App\Services\CaseServices\Finance\CaseFinanceService;
use medcenter24\mcCore\App\Services\Entity\FinanceConditionService;
use medcenter24\mcCore\App\Transformers\FinanceConditionTransformer;
use League\Fractal\TransformerAbstract;

class FinanceConditionController extends ModelApiController
{
    #[Pure] protected function getDataTransformer(): TransformerAbstract
    {
        return new FinanceConditionTransformer();
    }

    protected function getModelService(): ModelService|FinanceConditionService
    {
        return $this->getServiceLocator()->get(FinanceConditionService::class);
    }

    protected function getRequestClass(): string
    {
        return FinanceConditionRequest::class;
    }

    /**
     * Add new rule
     * @param JsonRequest $request
     * @return Response
     * @throws NotImplementedException
     */
    public function store(JsonRequest $request): Response
    {
        /** @var FinanceConditionRequest $request */
        $request = call_user_func([$this->getRequestClass(), 'createFromBase'], $request);
        $request->validate();

        /** @var CaseFinanceService $caseFinanceService */
        $caseFinanceService = $this->getServiceLocator()->get(CaseFinanceService::class);
        $financeCondition = $caseFinanceService->updateFinanceConditionByRequest($request);
        return $this->response->created(
            url("pages/settings/finance/{$financeCondition->id}"),
            [self::API_DATA_PARAM => $this->getDataTransformer()->transform($financeCondition)]
        );
    }

    /**
     * Update existing rule
     * @param int $id
     * @param JsonRequest $request
     * @return Response
     */
    public function update(int $id, JsonRequest $request): Response
    {
        /** @var JsonRequest $request */
        $request = call_user_func([$this->getRequestClass(), 'createFromBase'], $request);
        $request->validate();
        $caseFinanceService = $this->getServiceLocator()->get(CaseFinanceService::class);
        $financeCondition = $caseFinanceService->updateFinanceConditionByRequest($request, $id);
        return $this->response->item($financeCondition, new FinanceConditionTransformer());
    }
}
