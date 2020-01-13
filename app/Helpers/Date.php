<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2020 (original work) MedCenter24.com;
 */

namespace medcenter24\mcCore\App\Helpers;


use Illuminate\Support\Carbon;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;

class Date
{
    use ServiceLocatorTrait;

    public static function sysDate(Carbon $date = null, string $tz = null): string
    {
        $str = '';
        if ($date) {
            if ($tz) {
                $date->setTimezone($tz);
            }
            $str = $date->format(config('date.systemFormat'));
        }
        return $str;
    }
}