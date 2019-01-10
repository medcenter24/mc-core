<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Models\Formula\Variables;


use App\Contract\Formula\Variable;

class Decimal implements Variable
{
    /**
     * @var float
     */
    private $var;

    /**
     * @var int
     */
    private $precision;

    /**
     * Decimal constructor.
     * @param $var
     * @param int $precision
     */
    public function __construct($var, int $precision = 2)
    {
        $this->var = round((float) $var, $precision);
        $this->precision = $precision;
    }

    public function getVar()
    {
        return $this->var;
    }

    public function getResult()
    {
        return $this->getVar();
    }

    public function varView(): string
    {
        return sprintf('%0.'.$this->precision.'f', $this->getVar());
    }
}
