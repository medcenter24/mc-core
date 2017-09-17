<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Helpers;


class Number
{
    public static function toNumber($str = '')
    {
        $value = preg_replace('/[^0-9,\.]/', '', $str);
        $value = str_replace(',', '.', $value);
        $value *= 1;
        return $value;
    }
}
