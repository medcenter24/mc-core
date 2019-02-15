<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Helpers;


use App\Exceptions\InconsistentDataException;

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
     * @return array
     */
    public static function convertTableToKeyValue (array $arr)
    {
        $keys = array_shift($arr);
        return array_map(function ($val) use ($keys) {

            if (count($keys) != count($val)) {
                throw new InconsistentDataException('Key and Data tables should have the same size');
            }

            return array_combine($keys, $val);
        }, $arr);
    }

    public static function collectTableRows (array $arr)
    {
        $result = [];
        foreach (self::convertTableToKeyValue($arr) as $row) {
            foreach ($row as $key => $value) {
                if (!isset($result[$key])) {
                    $result[$key] = [];
                }
                $result[$key][] = $value;
            }
        }

        return $result;
    }

    /**
     * Checks if an array has value and set default empty value if not
     * @param array $arr
     * @param string $key
     * @param string $defaultEmpty
     */
    public static function setDefault(array &$arr, string $key, $defaultEmpty = '')
    {
        if (!isset($arr[$key]) || !$arr[$key]) {
            $arr[$key] = $defaultEmpty;
        }
    }
}
