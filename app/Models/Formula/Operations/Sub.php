<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Models\Formula\Operations;


class Sub extends AbstractOperation
{
    /**
     * @return string
     */
    public function getLeftSignView(): string
    {
        return ' - ';
    }

    public function runOperation($result)
    {
        return $result - $this->variable->getResult();
    }
}
