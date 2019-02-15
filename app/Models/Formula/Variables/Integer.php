<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Models\Formula\Variables;


use App\Contract\Formula\Variable;

class Integer implements Variable
{
    /**
     * @var int
     */
    private $var;

    public function __construct($var)
    {
        $this->var = (int) $var;
    }

    public function getVar(): int
    {
        return $this->var;
    }

    public function getResult(): int
    {
        return $this->getVar();
    }

    public function varView(): string
    {
        return sprintf('%d', $this->getVar());
    }
}
