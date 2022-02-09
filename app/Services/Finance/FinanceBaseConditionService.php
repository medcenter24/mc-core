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

namespace medcenter24\mcCore\App\Services\Finance;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use medcenter24\mcCore\App\Entity\FinanceCondition;
use medcenter24\mcCore\App\Services\Entity\AbstractModelService;
use medcenter24\mcCore\App\Services\Entity\FinanceConditionService;

class FinanceBaseConditionService
{
    /**
     * There is only 1 correct base condition, other should be ignored
     * 1. If we have equal conditions we will select with bigger price and log error about misconfiguration
     * 2. If we have condition with percent - it is deleted as not relevant and log error
     * 3. Condition should be selected with the highest number of matches
     *
     * @param Collection $conditions
     * @param array $matches
     * @return Collection
     */
    public function filterBaseCondition(Collection $conditions, array $matches = []): Collection
    {
        $selectedConditions = new Collection();
        $baseCondition = null;
        /** @var FinanceCondition $condition */
        foreach ($conditions as $condition) {
            if ($this->isBaseCondition($condition)) {
                if (!$this->isCurrencyCondition($condition)) {
                    Log::error(sprintf('FinanceCondition "%s" has currency_mode set to NOT currency and can not be BASE type',
                        $condition->getAttribute(AbstractModelService::FIELD_ID)
                    ));
                    continue;
                }

                $baseCondition = $this->chooseBetterBaseCondition($condition, $baseCondition, $matches);
            } else {
                $selectedConditions->add($condition);
            }
        }

        if ($baseCondition) {
            $selectedConditions->prepend($baseCondition);
        }

        return $selectedConditions;
    }

    private function isBaseCondition(FinanceCondition $condition): bool
    {
        return $condition->getAttribute(FinanceConditionService::FIELD_TYPE)
            === FinanceConditionService::PARAM_TYPE_BASE;
    }

    private function isCurrencyCondition(FinanceCondition $condition): bool
    {
        return $condition->getAttribute(FinanceConditionService::FIELD_CURRENCY_MODE)
            === FinanceConditionService::PARAM_CURRENCY_MODE_CURRENCY;
    }

    private function chooseBetterBaseCondition(
        FinanceCondition $condition,
        ?FinanceCondition $baseCondition,
        array $matches = []
    ): FinanceCondition {

        if (!$baseCondition) {
            return $condition;
        }

        $match1 = $this->getCountMatches($condition, $matches);
        $match2 = $this->getCountMatches($baseCondition, $matches);
        if ($match1 !== $match2) {
            return $match1 > $match2 ? $condition : $baseCondition;
        }

        return $this->getValue($condition) > $this->getValue($baseCondition)
            ? $condition
            : $baseCondition;
    }

    private function getCountMatches(FinanceCondition $condition, array $matches = []): int
    {
        $id = $condition->getAttribute(AbstractModelService::FIELD_ID);
        if (array_key_exists($id, $matches)) {
            return $matches[$id];
        }
        return 0;
    }

    private function getValue(FinanceCondition $condition): float
    {
        return (float) $condition->getAttribute(FinanceConditionService::FIELD_VALUE);
    }
}
