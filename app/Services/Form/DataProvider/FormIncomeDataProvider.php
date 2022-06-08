<?php
/*
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
 * Copyright (c) 2022 (original work) MedCenter24.com;
 */

declare(strict_types=1);

namespace medcenter24\mcCore\App\Services\Form\DataProvider;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Services\CaseServices\Finance\CaseFinanceViewService;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;
use medcenter24\mcCore\App\Services\Entity\AbstractModelService;
use Throwable;

class FormIncomeDataProvider
{
    use ServiceLocatorTrait;

    private array $incomeCache = [];

    /**
     * @param Model $model
     * @return Collection|null
     */
    private function getIncome(Model $model): ?Collection
    {
        if (!array_key_exists($model->getAttribute(AbstractModelService::FIELD_ID), $this->incomeCache)) {
            try {
                $this->incomeCache[$model->getAttribute(AbstractModelService::FIELD_ID)]
                    = $model instanceof Accident
                    ? $this->getCaseFinanceViewService()->get($model, ['income'])->first()
                    : null;
            } catch (Throwable $e) {
                Log::error($e->getMessage(), [$e]);
                $this->incomeCache[$model->getAttribute(AbstractModelService::FIELD_ID)] = null;
            }
        }
        return $this->incomeCache[$model->getAttribute(AbstractModelService::FIELD_ID)];
    }

    /**
     * @param Model $model
     * @return string
     */
    public function getIncomeActiveValue(Model $model): string
    {
        $income = $this->getIncome($model);
        return (string)$income?->get('finalActiveValue');
    }

    public function getCurrencyTitle(Model $model): string
    {
        $income = $this->getIncome($model);
        return (string)$income?->get('currency')->title;
    }

    public function getCurrencyIco(Model $model): string
    {
        $income = $this->getIncome($model);
        return (string)$income?->get('currency')->ico;
    }

    private function getCaseFinanceViewService(): CaseFinanceViewService
    {
        return $this->getServiceLocator()->get(CaseFinanceViewService::class);
    }
}
