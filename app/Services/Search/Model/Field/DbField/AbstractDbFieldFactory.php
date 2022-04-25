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

abstract class AbstractDbFieldFactory
{
    public function create(string $order): SearchDbField
    {
        return new SearchDbField(
            $this->isJoin(),
            $this->getJoinTable(),
            $this->getJoinFirst(),
            $this->getJoinSecond(),
            $this->getSelectField(),
            in_array($order, ['asc', 'desc']),
            $order,
        );
    }

    /**
     * @return bool
     */
    abstract protected function isJoin(): bool;

    /**
     * @return string
     */
    abstract protected function getJoinTable(): string;

    /**
     * @return string
     */
    abstract protected function getJoinFirst(): string;

    /**
     * @return string
     */
    abstract protected function getJoinSecond(): string;

    /**
     * @return string
     */
    abstract protected function getSelectField(): string;
}
