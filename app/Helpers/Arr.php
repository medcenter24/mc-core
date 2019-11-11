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
    public static function multiArrayToString ($arr): string
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
    public static function convertTableToKeyValue (array $arr): array
    {
        $keys = array_shift($arr);
        return array_map(static function ($val) use ($keys) {

            if (count($keys) !== count($val)) {
                throw new InconsistentDataException('Key and Data tables should have the same size');
            }

            return array_combine($keys, $val);
        }, $arr);
    }

    /**
     * @param array $arr
     * @return array
     */
    public static function collectTableRows (array $arr): array
    {
        $result = [];
        foreach (self::convertTableToKeyValue($arr) as $row) {
            foreach ($row as $key => $value) {
                if (!array_key_exists($key, $result)) {
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
    public static function setDefault(array &$arr, string $key, $defaultEmpty = ''): void
    {
        if (!isset($arr[$key]) || !$arr[$key]) {
            $arr[$key] = $defaultEmpty;
        }
    }

    /**
     * Checks that nested key exists in the nested array
     *
     * @example keysExists([ 0 => [ 0 => [ 0 => 0 ]]], [0, 1, 0]) === true
     *
     * @param array $data
     * @param array $keys
     * @return bool
     */
    public static function keysExists(array $data, array $keys): bool
    {
        $array = $data;
        foreach ($keys as $key) {
            if (!array_key_exists($key, $array)) {
                return false;
            }

            $array = $array[$key];
        }

        return true;
    }

    /**
     * Convert any array (associative) to the values only (with numbers in the key)
     * @param array $table
     * @return array
     */
    public static function recursiveValues(array $table): array
    {
        foreach ($table as $key => $sub) {
            if (is_array($sub)) {
                $table[$key] = self::recursiveValues($sub);
            }
        }
        return array_values($table);
    }
}
