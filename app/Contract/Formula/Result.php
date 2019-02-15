<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Contract\Formula;


interface Result
{
    /**
     * Calculate and get result
     * @param FormulaBuilder $formulaBuilder
     * @return int|float
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function calculate(FormulaBuilder $formulaBuilder);
}