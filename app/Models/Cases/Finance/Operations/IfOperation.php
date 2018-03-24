<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Models\Cases\Finance\Operations;


class IfOperation
{
    /**
     * @var string
     */
    private $modelName;

    /**
     * @var int
     */
    private $id;

    /**
     * IfOperation constructor.
     * @param string $modelName
     * @param int $id
     */
    public function __construct(string $modelName, int $id)
    {
        $this->modelName = $modelName;
        $this->id = $id;
    }

    public function modelName()
    {
        return $this->modelName;
    }

    public function id()
    {
        return $this->id;
    }
}
