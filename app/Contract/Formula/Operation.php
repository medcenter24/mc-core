<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Contract\Formula;


interface Operation
{
    /**
     * @return string
     */
    public function varView() : string ;

    /**
     * @param bool $visible
     * @return string
     */
    public function leftSignView(bool $visible = true) : string ;

    /**
     * @param bool $visible
     * @return string
     */
    public function rightSignView(bool $visible = true) : string ;

    /**
     * Getting stored variable
     * @return FormulaBuilder|Variable
     */
    public function getVar();

    /**
     * Weight of the action (mul|div|percent needs to be done firstly)
     * @return  int
     */
    public function getWeight(): int ;

    /**
     * Runs operations between variable and a result
     * @param $result
     * @return mixed
     */
    public function runOperation($result);
}
