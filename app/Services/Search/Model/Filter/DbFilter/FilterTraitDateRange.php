<?php
/*
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
 * Copyright (c) 2022 (original work) MedCenter24.com;
 */

declare(strict_types=1);

namespace medcenter24\mcCore\App\Services\Search\Model\Filter\DbFilter;

trait FilterTraitDateRange
{
    protected function getWhereOperation(): string
    {
        return 'BETWEEN';
    }

    protected function getValues(mixed $values): array
    {
        $dates = [];
        $value = current($values);
        if (is_string($value) && mb_strpos('>', $value)) {
            [$from, $to] = explode('>', $value);
            $dates[] = $from . ' 00:00:00';
            $dates[] = $to . ' 23:59:59';
        }
        return $dates;
    }

    /**
     * @param mixed $whereValue
     * @return bool
     */
    protected function getLoaded(mixed $whereValue): bool
    {
        return !empty($this->getValues($whereValue));
    }
}
