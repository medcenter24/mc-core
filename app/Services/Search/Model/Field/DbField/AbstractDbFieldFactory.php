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

namespace medcenter24\mcCore\App\Services\Search\Model\Field\DbField;

use medcenter24\mcCore\App\Services\Search\Model\Field\SearchDbField;
use medcenter24\mcCore\App\Services\Search\Model\Field\SearchField;

abstract class AbstractDbFieldFactory
{
    public function create(SearchField $searchField): SearchDbField
    {
        return new SearchDbField(
            $this->getTableName(),
            $this->getSelectField(),
            $searchField->getId(),
            in_array($searchField->getOrder(), ['asc', 'desc']),
            $searchField->getOrder(),
            $this->getJoins(),
        );
    }

    /**
     * @return string
     */
    abstract protected function getTableName(): string;

    /**
     * @return string
     */
    protected function getSelectField(): string {
        $fields = $this->getSelectFieldParts();
        return sprintf('%s.%s', $fields[0], $fields[1]);
    }

    abstract protected function getSelectFieldParts();

    protected function getJoins(): array
    {
        return [];
    }
}
