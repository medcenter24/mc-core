<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Models\Formula;


interface FormulaResultable
{
    /**
     * Count result for that formula
     * @return mixed
     */
    public function getResult();
}