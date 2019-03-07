<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\Formula;


use App\Contract\Formula\FormulaBuilder;
use App\Contract\Formula\Operation;
use ArrayIterator;
use Throwable;

class FormulaViewService
{
    /**
     * Returns string where is row performed as a linear string
     * @param FormulaBuilder $formula
     * @return string
     * @throws Throwable
     */
    public function render(FormulaBuilder $formula): string
    {
        $strFormula = '';
        $selfObj = $this;
        // do not change real formula
        $formulaC = clone $formula;
        $formulaC->getFormulaCollection()->each(function (Operation $operation, $key) use (&$strFormula, $selfObj) {
            $var = $operation->getVar();
            if ($var instanceof FormulaBuilder) {
                $part = $selfObj->render($var);
                if ($part && !preg_match('/^[0-9\.\-]+$/', $part)) {
                    $part = '( ' . $part . ' )';
                }
            } else {
                $part = $operation->varView();
            }

            if ($operation->leftSignView((bool) $key)) {
                $strFormula .= $operation->leftSignView();
            }

            $strFormula .= $part ?: '0';
            /*if ($part) {
                $strFormula .= preg_match('/^[0-9\.\-]+$/', $part) ? $part : '( ' . $part . ' )';
            } else {
                $strFormula .= '0';
            }*/

            if ($operation->rightSignView()) {
                $strFormula .= $operation->rightSignView();
            }
        });

        return $strFormula;
    }
}