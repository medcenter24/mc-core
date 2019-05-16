<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

namespace medcenter24\mcCore\App\Services;


use medcenter24\mcCore\App\User;

/**
 * Permissions and role interface
 *
 * Class RoleService
 * @package medcenter24\mcCore\App\Services
 */
class RoleService
{
    const LOGIN_ROLE = 'login';
    const DIRECTOR_ROLE = 'director';
    const DOCTOR_ROLE = 'doctor';
    const ADMIN_ROLE = 'admin';

    const ROLES = [
        self::LOGIN_ROLE,
        self::DIRECTOR_ROLE,
        self::DIRECTOR_ROLE,
        self::ADMIN_ROLE,
    ];

    /**
     * Check that user has role permissions
     *
     * @param User $user
     * @param string $role
     * @return mixed
     */
    public function hasRole(User $user = null, string $role = ''): bool
    {
        return $user ? $user->roles()->where('title', $role)->count() : false;
    }

    public function isValidRoles(array $roles): bool
    {
        return !count(array_diff($roles, self::ROLES));
    }
}
