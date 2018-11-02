<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\Formula;


use App\Models\Formula\FormulaBuilderInterface;
use App\Models\Formula\Operation;

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
        $collection = $formula->getFormulaCollection()->getIterator();
        while ($collection->valid()) {
            /** @var Operation $operation */
            $operation = $collection->current();
            $var = $operation->getResult();
            if ($var instanceof FormulaBuilderInterface ) {
                $strFormula .= '( ' . $this->render($var) . ' )';
            } else {
                $strFormula .= $operation->varView();
            }
            $result = $operation->appendTo($result);
            $collection->next();
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