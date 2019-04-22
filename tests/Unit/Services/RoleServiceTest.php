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

namespace medcenter24\mcCore\Tests\Unit\Services;

use medcenter24\mcCore\App\Role;
use medcenter24\mcCore\App\Services\RoleService;
use medcenter24\mcCore\App\User;
use medcenter24\mcCore\Tests\TestCase;

class RoleServiceTest extends TestCase
{
    /**
     * @var RoleService
     */
    private $service;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new RoleService();

        $role = $this->prophesize(Role::class)->reveal();
        $role->title = 'roleName';

        $storage = new class {
            private $sameRole = false;
            public function where($title, $role)
            {
                $this->sameRole = $role == 'roleName';
                return $this;
            }
            public function count() {
                return $this->sameRole;
            }
        };

        $user = $this->prophesize(User::class);
        $user->roles()->willReturn($storage);

        $this->user = $user->reveal();
    }

    public function testHasRole()
    {
        self::assertTrue($this->service->hasRole($this->user, 'roleName'));
        self::assertFalse($this->service->hasRole($this->user, 'incorrectRoleName'));
    }
}
