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


class Paginator extends RequestBuilder
{
    private const FIELD_OFFSET = 'offset';
    private const FIELD_LIMIT = 'limit';

    private const DEFAULT_OFFSET = 0;
    private const DEFAULT_LIMIT = 25;

    private $offset = self::DEFAULT_OFFSET;
    private $limit = self::DEFAULT_LIMIT;

    /**
     * @param array $paginationConf
     * ['fields' => [{field: 'offset', 'value': 10}, {field: 'limit', value: 25}]]
     */
    /**
     * @param array $paginationConf
     */
    public function inject(array $paginationConf): void
    {
        parent::inject($paginationConf);
        $this->setOffset();
        $this->setLimit();
    }

    public function getOffset(): int {
        return $this->offset;
    }

    public function getLimit(): int {
        return $this->limit;
    }

    private function setOffset(): void {
        $this->offset = (int) $this->getFieldValue(self::FIELD_OFFSET, (string) self::DEFAULT_OFFSET);
    }

    private function setLimit(): void {
        $this->limit = (int)$this->getFieldValue(self::FIELD_LIMIT, self::DEFAULT_LIMIT);
    }
}