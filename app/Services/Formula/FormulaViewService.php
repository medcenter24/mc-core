<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\Formula;


use App\Models\Formula\FormulaBuilderInterface;
use Illuminate\Support\Collection;

class FormulaViewService
{
    /**
     * Returns string where is row performed as a linear string
     * @param FormulaBuilderInterface $formula
     * @return string
     * @throws \Throwable
     */
    public function render(FormulaBuilderInterface $formula)
    {
        $collection = $formula->getFormulaCollection()->getIterator();
        return $this->showFormula($collection);
    }

    /**
     * @param \ArrayIterator $collection
     * @return string
     * @throws \Throwable
     */
    private function showFormula(\ArrayIterator $collection)
    {
        $strFormula = '';
        if ($collection->count()) {
            $first = true;
            while ($collection->valid()) {
                /** @var \App\Models\Formula\Operation $operation */
                $operation = $collection->current();

                if ($operation->leftSignView(!$first)) {
                    $strFormula .= $operation->leftSignView();
                }

                $var = $operation->varView();
                $strFormula .= $var instanceof FormulaBuilderInterface
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
