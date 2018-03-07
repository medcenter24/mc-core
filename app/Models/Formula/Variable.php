<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Models\Formula;


interface Variable extends FormulaResultable
{
    /**
     * Variable constructor.
     * initialize variable and convert it to specified type
     * @param $var
     */
    public function __construct($var);

    /**
     * Return view (string) of this formatted variable
     * @return string
     */
    public function varView();

    /**
     * Return variable formatted to their type
     * @return mixed
     */
    public function getVar();
}
