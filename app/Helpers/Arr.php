<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

namespace medcenter24\mcCore\App\Helpers;


use medcenter24\mcCore\App\Exceptions\InconsistentDataException;

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
