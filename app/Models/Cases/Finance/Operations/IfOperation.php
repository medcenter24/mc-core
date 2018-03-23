<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Models\Cases\Finance\Operations;


use App\Accident;

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

    /**
     * Check that condition confirms to the accident
     * @param Accident $accident
     */
    public function valid(Accident $accident)
    {

    }
}
