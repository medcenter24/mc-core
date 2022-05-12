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

declare(strict_types=1);

namespace medcenter24\mcCore\App\Services\Formula;

use medcenter24\mcCore\App\Contract\Formula\FormulaBuilder;
use medcenter24\mcCore\App\Contract\Formula\Operation;
use medcenter24\mcCore\App\Contract\Formula\Result;
use medcenter24\mcCore\App\Models\Formula\Exception\FormulaException;
use medcenter24\mcCore\App\Models\Formula\Variables\Decimal;

class FormulaResultService implements Result
{
    /**
     * Calculate and get result
     * @param FormulaBuilder $formulaBuilder
     * @return int|float
     * @throws FormulaException
     */
    public function calculate(FormulaBuilder $formulaBuilder): float|int
    {
        $result = false;
        // do not change the real formula, pls
        $formulaC = clone $formulaBuilder;
        $collection = $formulaC->getFormulaCollection();
        $collection = $collection->sortByDesc(function (Operation $op, $key) {
            if (!$key) {
                // first operation in the row doesn't have any sense - this is just a variable,
                // but it should be first in the row
                return 1000;
            }
            return $op->getWeight();
        });
        $iterator = $collection->getIterator();
        while ($iterator->valid()) {
            /** @var Operation $operation */
            $operation = $iterator->current();

            $var = $operation->getVar();
            if ($var instanceof FormulaBuilder) {
                $partRes = $this->calculate($var);
                if ($result === false) {
                    $result = $partRes;
                } else {
                    $newOpClass = get_class($operation);
                    $newOp = new $newOpClass(new Decimal($partRes));
                    $result = $newOp->runOperation($result);
                }
            } else {
                $result = $result === false ? $operation->getVar()->getResult() : $operation->runOperation($result);
            }

            $iterator->next();
        }

        return $result === false ? 0 : $result;
    }
}
