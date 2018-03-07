<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Models\Formula;


use App\Models\Formula\Exception\FormulaException;

interface Operation
{
    /**
     * Apply the operation to the result
     * @param int|float|bool $result
     * @return int|float Result
     * @throws FormulaException
     */
    public function appendTo($result = false);

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
}
