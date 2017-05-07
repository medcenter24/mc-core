<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Helpers;


class Arr
{
    /**
     * Glue all data from array to string
     *
     * @param $arr
     * @return string
     */
    public static function multiArrayToString ($arr)
    {
        $result = [];
        array_walk_recursive($arr, function($v) use (&$result) {
            $result[] = $v;
        });
        return implode(' ', $result);
    }

    /**
     * Create new array, where 'key' from first row and 'values' all other rows
     * @param $arr
     */
    public static function convertToKeyValue (array $arr) {
        // 1. keys
        $keys = array_shift($arr);
        foreach ($keys as $key) {

        }
    }
}
