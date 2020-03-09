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

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Contract\General\Service;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface ModelService
{
    /**
     * @return Model
     */
    public function getModel(): Model;

    /**
     * @param array $data
     * @return Model
     */
    public function create(array $data = []): Model;

    /**
     * @param array $filterByFields
     * @param array $data
     * @return Model
     */
    public function findAndUpdate(array $filterByFields, array $data): Model;

    /**
     * @param array $data
     * @return Model
     */
    public function firstOrCreate(array $data = []): Model;

    /**
     * @param array $filters
     * @return Model|null
     */
    public function first(array $filters = []): ?Model;

    /**
     * @param array $filters
     * @return Collection
     */
    public function search(array $filters = []): Collection;

    /**
     * @param array $filters
     * @return int
     */
    public function count(array $filters = []): int;

    /**
     * @param $id
     * @return bool
     */
    public function delete($id): bool;
}
