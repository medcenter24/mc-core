<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\Formula;


use App\Models\Formula\Exception\FormulaException;
use App\Models\Formula\Formula;
use App\Models\Formula\FormulaBuilderInterface;

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
        return view('formula.formulaRow', compact('collection'))->render();
    }
}
