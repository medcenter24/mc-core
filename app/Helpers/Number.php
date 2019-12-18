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


class Number
{
    public static function toNumber($str = '')
    {
        $value = preg_replace('/[^0-9,.]/', '', $str);
        $value = str_replace(',', '.', $value);
        $value = trim($value, ',.');

        if (mb_substr_count($value, '.') > 1) { // trying to make it less expensive for CPU
            $parts = explode('.', $value);
            if (count($parts) > 2) {
                // leave only the very first dot in the number
                $p1 = array_shift($parts);
                $p1 = (int) $p1;
                if (count($parts)) {
                    $value = $p1 . '.' . implode('', $parts);
                }
            }
        }

        if (is_string($value) && empty($value)) {
            $value = 0;
        }

        try {
            $value *= 1; // convert to number
        } catch (\Exception $e) {
        }
        return $value;
    }
}
