<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\Formula;


use App\Contract\Formula\FormulaBuilder;
use App\Contract\Formula\Operation;
use App\Contract\Formula\Result;
use App\Models\Formula\Variables\Decimal;

class FormulaResultService implements Result
{
    /**
     * Calculate and get result
     * @param FormulaBuilder $formula
     * @return int|float
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function calculate(FormulaBuilder $formula)
    {
        $result = false;
        $collection = $formula->getFormulaCollection();
        $collection = $collection->sortByDesc(function (Operation $op, $key) {
            if (!$key) {
                // first operation in the row doesn't have any sense - this is just a variable
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
                    /** @var Operation $newOp */
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