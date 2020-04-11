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

namespace medcenter24\mcCore\App\Services\DoctorLayer;

use Illuminate\Database\Eloquent\Model;
use medcenter24\mcCore\App\Services\Entity\RoleService;
use Illuminate\Database\Eloquent\Collection;

trait FiltersTrait
{
    /**
     * @param int $doctorId
     * @return Collection
     */
    public function getActiveByDoctor(int $doctorId): Collection
    {
        /** @var Collection $collection */
        $collection = $this->getQuery()->orderBy('title')->get();
        /** @var RoleService $roleService */
        $roleService = $this->getServiceLocator()->get(RoleService::class);
        return $collection->filter(static function(Model $model) use ($doctorId, $roleService) {
            return ($model->getAttribute('status') === self::STATUS_ACTIVE
                    && $roleService->hasRole($model->creator, RoleService::DIRECTOR_ROLE))
                || (int)$model->getAttribute('created_by') === $doctorId;
        });
    }
}
