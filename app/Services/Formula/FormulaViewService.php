<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\Formula;


use App\Contract\Formula\FormulaBuilder;
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
        $collection = $formula->getFormulaCollection()->getIterator();
        $strFormula = '';
        if ($collection->count()) {
            $first = true;
            while ($collection->valid()) {
                /** @var \App\Contract\Formula\Operation $operation */
                $operation = $collection->current();
                $var = $operation->getVar();
                if ($var instanceof FormulaBuilder) {
                    $part = $this->render($var);
                } else {
                    $part = $operation->varView();
                }


                if ($operation->leftSignView(!$first)) {
                    $strFormula .= $operation->leftSignView();
                }

                if ($part) {
                    $strFormula .= preg_match('/^[0-9\.\-]+$/', $part) ? $part : '( ' . $part . ' )';
                } else {
                    $strFormula .= '0';
                }

                if ($operation->rightSignView()) {
                    $strFormula .= $operation->rightSignView();
                }

                $collection->next();
                $first = false;
            }
        }

        return $strFormula;
    }
}
