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

namespace medcenter24\mcCore\App\Services\Entity;

use medcenter24\mcCore\App\Entity\Role;
use medcenter24\mcCore\App\Entity\User;

/**
 * Permissions and role interface
 *
 * Class RoleService
 * @package medcenter24\mcCore\App\Services
 */
class RoleService extends AbstractModelService
{
    public const LOGIN_ROLE = 'login';
    public const DIRECTOR_ROLE = 'director';
    public const DOCTOR_ROLE = 'doctor';
    public const ADMIN_ROLE = 'admin';

    public const ROLES = [
        self::LOGIN_ROLE,
        self::DIRECTOR_ROLE,
        self::DOCTOR_ROLE,
        self::ADMIN_ROLE,
    ];

    public const FIELD_TITLE = 'title';

    public const FILLABLE = [self::FIELD_TITLE];
    public const UPDATABLE = [self::FIELD_TITLE];

    private array $grantedRoles = [];

    /**
     * Check that user has role permissions
     *
     * @param User|null $user
     * @param string $role
     * @return bool
     */
    public function hasRole(User $user = null, string $role = ''): bool
    {
        if (empty($this->grantedRoles[$user->id]) || !array_key_exists($role, $this->grantedRoles[$user->id])) {
            $roles = $user->roles()->pluck(RoleService::FIELD_TITLE)->toArray();
            $this->grantedRoles[$user->id][$role] = in_array($role, $roles);
        }
        return $this->grantedRoles[$user->id][$role];
    }

    public function isValidRoles(array $roles): bool
    {
        return !count(array_diff($roles, self::ROLES));
    }

    /**
     * @inheritDoc
     */
    protected function getClassName(): string
    {
        return Role::class;
    }

    /**
     * @inheritDoc
     */
    protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_TITLE => '',
        ];
    }
}
