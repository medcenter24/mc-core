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

declare(strict_types=1);

namespace medcenter24\mcCore\App\Services\Entity\Traits;

use Illuminate\Database\Eloquent\Model;
use medcenter24\mcCore\App\Entity\User;
use medcenter24\mcCore\App\Services\Entity\RoleService;

trait Access
{
    /**
     * @param User $user
     * @param Model $model
     * @return bool
     */
    public function hasAccess(User $user, Model $model): bool
    {
        $createdBy = (int) $model->getAttribute(self::FIELD_CREATED_BY);
        $userId = (int) $user->getKey();
        $owner = $createdBy && $userId && $createdBy === $userId;
        $hasDirectorRole = $this->getRoleService()->hasRole($user, RoleService::DIRECTOR_ROLE);
        $hasAdminRole = $this->getRoleService()->hasRole($user, RoleService::ADMIN_ROLE);
        return $hasDirectorRole || $hasAdminRole || $owner;
    }

    /**
     * @return RoleService
     */
    private function getRoleService(): RoleService
    {
        return $this->getServiceLocator()->get(RoleService::class);
    }
}
