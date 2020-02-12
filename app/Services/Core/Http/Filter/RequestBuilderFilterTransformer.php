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

namespace medcenter24\mcCore\App\Services\Core\Http\Filter;


use medcenter24\mcCore\App\Services\Core\Http\Builders\Filter;

/**
 * Transform filter to the expected shape
 * Class RequestBuilderFilterTransformer
 * @package medcenter24\mcCore\App\Services\Core\Http\Filter
 */
class RequestBuilderFilterTransformer
{
    public function transform(array $filter): array
    {
        $filter = $this->transformValue($filter);
        $filter = $this->transformMatch($filter);
        return $filter;
    }

    private function transformValue(array $filter): array
    {
        switch ($filter[Filter::FIELD_EL_TYPE]) {
            case Filter::TYPE_DATE_RANGE:
                $value = $filter[Filter::FIELD_VALUE];
                if (mb_strpos($value, Filter::DATE_SEPARATOR) !== false) {
                    $filter[Filter::FIELD_VALUE] = explode(Filter::DATE_SEPARATOR, $value);
                    foreach ($filter[Filter::FIELD_VALUE] as $k => $item) {
                        // default times if not provided
                        if (mb_strpos($item, ':') === false) {
                            $filter[Filter::FIELD_VALUE][$k] .= !$k ? ' 00:00:00' : ' 23:59:59';
                        }
                    }
                    $filter[Filter::FIELD_MATCH] = Filter::MATCH_BETWEEN;
                } elseif (!in_array($filter[Filter::FIELD_MATCH], [
                    Filter::MATCH_LESS_EQUAL,
                    Filter::MATCH_LESS,
                    Filter::MATCH_GREATER,
                    Filter::MATCH_GREATER_EQUAL,
                    Filter::MATCH_EQ,
                ], true)) {
                    $filter[Filter::FIELD_MATCH] = Filter::MATCH_GREATER;
                }
                break;
            case Filter::TYPE_SELECT:
                if ($filter[Filter::FIELD_MATCH] === Filter::MATCH_IN) {
                    $filter[Filter::FIELD_VALUE] = explode(',', $filter[Filter::FIELD_VALUE]);
                }
                break;
        }

        return $filter;
    }

    private function transformMatch(array $filter): array
    {
        if (!array_key_exists(Filter::FIELD_MATCH, $filter)) {
            $filter[Filter::FIELD_MATCH] = Filter::MATCH_EQ;
        }

        switch ($filter[Filter::FIELD_MATCH]) {
            case Filter::MATCH_GREATER:
                $filter[Filter::FIELD_MATCH] = '>';
                break;
            case Filter::MATCH_GREATER_EQUAL:
                $filter[Filter::FIELD_MATCH] = '>=';
                break;
            case Filter::MATCH_LESS:
                $filter[Filter::FIELD_MATCH] = '<';
                break;
            case Filter::MATCH_LESS_EQUAL:
                $filter[Filter::FIELD_MATCH] = '<=';
                break;
            case Filter::MATCH_CONTENTS:
                $filter[Filter::FIELD_MATCH] = 'ilike';
                $filter[Filter::FIELD_VALUE] = '%'.$filter[Filter::FIELD_VALUE].'%';
                break;
            case Filter::MATCH_ENDS_WITH:
                $filter[Filter::FIELD_MATCH] = 'ilike';
                $filter[Filter::FIELD_VALUE] = '%'.$filter[Filter::FIELD_VALUE];
                break;
            case Filter::MATCH_START_WITH:
                $filter[Filter::FIELD_MATCH] = 'ilike';
                $filter[Filter::FIELD_VALUE] .= '%';
                break;
            case Filter::MATCH_EQ:
                $filter[Filter::FIELD_MATCH] = '=';
                break;
        }

        return $filter;
    }
}
