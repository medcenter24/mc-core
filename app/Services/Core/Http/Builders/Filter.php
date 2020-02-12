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

namespace medcenter24\mcCore\App\Services\Core\Http\Builders;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use medcenter24\mcCore\App\Helpers\Date;

class Filter extends RequestBuilder
{

    public const FIELD_MATCH = 'match';
    public const FIELD_EL_TYPE = 'elType';

    public const DATE_SEPARATOR = '>';

    public const MATCH_EQ = 'eq';
    public const MATCH_LIKE = 'like';
    public const MATCH_START_WITH = 'like%';
    public const MATCH_ENDS_WITH = '%like';
    public const MATCH_CONTENTS = '%like%';
    public const MATCH_LESS = 'lt';
    public const MATCH_LESS_EQUAL = 'lte';
    public const MATCH_GREATER = 'gt';
    public const MATCH_GREATER_EQUAL = 'gte';
    public const MATCH_IN = 'in';
    public const MATCH_BETWEEN = 'between';

    private const MATCHES = [
        self::MATCH_EQ,
        self::MATCH_LIKE,
        self::MATCH_START_WITH ,
        self::MATCH_ENDS_WITH,
        self::MATCH_CONTENTS ,
        self::MATCH_LESS,
        self::MATCH_LESS_EQUAL,
        self::MATCH_GREATER,
        self::MATCH_GREATER_EQUAL,
        self::MATCH_IN,
        self::MATCH_BETWEEN,
    ];

    public const TYPE_TEXT = 'text';
    public const TYPE_DATE_RANGE = 'dateRange';
    public const TYPE_SELECT = 'select';

    private const TYPES = [
        self::TYPE_TEXT,
        self::TYPE_DATE_RANGE,
        self::TYPE_SELECT,
    ];

    /**
     * @var Collection
     */
    private $filters;

    public static function create(): Filter
    {
        return new self();
    }

    public function inject(array $config): void
    {
        parent::inject($config);
        $this->filters = $this->getFields();
        $this->filters = $this->filters->where(self::FIELD_VALUE, '!=', '');
        $this->filters = $this->filters->whereIn(self::FIELD_MATCH, self::MATCHES);
        $this->filters = $this->filters->whereIn(self::FIELD_EL_TYPE, self::TYPES);
        $this->filters = $this->filters->filter(static function ($el) {
            $valid = true;
            if ($el[self::FIELD_EL_TYPE] === self::TYPE_DATE_RANGE) {
                $value = $el[self::FIELD_VALUE];
                if (mb_strpos($value, self::DATE_SEPARATOR) !== false) {
                    $dates = explode(self::DATE_SEPARATOR, $value);
                    $valid = count($dates) === 2 && $valid;
                    foreach ($dates as $date) {
                        $valid = $valid && Date::validateDate($date);
                    }
                } else {
                    $valid = $valid && Date::validateDate($value);
                }
            }
            return $valid;
        });
    }

    public function getFilters(): Collection
    {
        return $this->filters ?? collect();
    }
}
