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

namespace medcenter24\mcCore\Tests\Feature\Api;


use medcenter24\mcCore\App\User;

trait LoggedUser
{
    /**
     * @var User
     */
    private $user;

    public function getUser()
    {
        if (!$this->user) {
            // allow all roles for the test (maybe in the future I'll mock it for work with roles)
            // but it could be another unit test for testing only role access
            \Roles::shouldReceive('hasRole')
                ->andReturnUsing(function () {
                    return true;
                });
            /** @var User $user */
            $this->user = factory(User::class)->create(['password' => bcrypt('foo')]);
        }
        return $this->user;
    }
}
