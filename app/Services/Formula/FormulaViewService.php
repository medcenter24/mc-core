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
        return $this->showFormula($collection);
    }

    /**
     * @param ArrayIterator $collection
     * @return string
     * @throws Throwable
     */
    private function showFormula(ArrayIterator $collection): string
    {
        $strFormula = '';
        if ($collection->count()) {
            $first = true;
            while ($collection->valid()) {
                /** @var \App\Contract\Formula\Operation $operation */
                $operation = $collection->current();
                $var = $operation->getVar();

                if ($operation->leftSignView(!$first)) {
                    $strFormula .= $operation->leftSignView();
                }

                $strFormula .= $var instanceof FormulaBuilder
                    ? '( ' . $this->render($var) . ' )'
                    : $operation->varView();

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
