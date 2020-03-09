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

declare(strict_types = 1);

namespace medcenter24\mcCore\Tests\Feature\Api;

use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;
use medcenter24\mcCore\App\Services\Entity\RoleService;
use medcenter24\mcCore\App\Services\Entity\UserService;
use medcenter24\mcCore\App\Entity\User;

trait LoggedUser
{
    use ServiceLocatorTrait;

    /**
     * @var User
     */
    private $user;

    public function getUser(array $roles = []): User
    {
        $user = factory(User::class)->create([
            'name' => 'PHPUnit',
            'password' => bcrypt('foo'),
        ]);

        if (!count($roles)) {
            $roles = [
                RoleService::DIRECTOR_ROLE,
                RoleService::DOCTOR_ROLE,
                RoleService::LOGIN_ROLE,
                RoleService::ADMIN_ROLE,
            ];
        }

        $roleModels = [];
        /** @var RoleService $roleService */
        $roleService = $this->getServiceLocator()->get(RoleService::class);
        foreach ($roles as $role) {
            $roleModels[] = $roleService->firstOrCreate([
                RoleService::FIELD_TITLE => $role,
            ])->getAttribute(RoleService::FIELD_ID);
        }
        $user->roles()->attach($roleModels);

        return $user;
    }
}
