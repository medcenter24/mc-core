<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Models\Formula\Variables;


use App\Models\Formula\Variable;

class Integer implements Variable
{
    /**
     * @var int
     */
    private $var;

    public function __construct($var)
    {
        $this->var = intval($var);
    }

    public function getVar()
    {
        return $this->var;
    }

    public function getResult()
    {
        return $this->getVar();
    }

    public function varView()
    {
        return sprintf('%d', $this->getVar());
    }
}
