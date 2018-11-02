<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Models\Formula;


interface Operation
{
    /**
     * @return string
     */
    public function varView();

    /**
     * @param bool $visible
     * @return string
     */
    public function leftSignView(bool $visible = true);

    /**
     * @param bool $visible
     * @return string
     */
    public function rightSignView(bool $visible = true);

    /**
     * Getting stored variable
     * @return mixed
     */
    public function getVar();

    /**
     * Weight of the action (mul|div|percent needs to be done firstly)
     * @return  int
     */
    public function getWeight();

    /**
     * Runs operations between variable and a result
     * @param $result
     * @return mixed
     */
    public function runOperation($result);
}
