<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\Formula;


use App\Models\Formula\FormulaBuilderInterface;
use App\Models\Formula\Operation;
use App\Models\Formula\Variables\Decimal;

class FormulaResultService
{
    /**
     * Calculate and get result
     * @param FormulaBuilderInterface $formula
     * @return int|float
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function calculate(FormulaBuilderInterface $formula)
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
            if ($var instanceof FormulaBuilderInterface) {
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

    /**
     * @param FormulaBuilderInterface $formula
     * @return float
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function toFloat(FormulaBuilderInterface $formula)
    {
        return floatval($this->calculate($formula));
    }

    /**
     * @param FormulaBuilderInterface $formula
     * @return int
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function toInteger(FormulaBuilderInterface $formula)
    {
        return intval($this->calculate($formula));
    }
}