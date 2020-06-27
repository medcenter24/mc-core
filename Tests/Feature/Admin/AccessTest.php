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
declare(strict_types=1);

namespace medcenter24\mcCore\Tests\Feature\Admin;

use medcenter24\mcCore\App\Entity\User;
use medcenter24\mcCore\Tests\TestCase;

class AccessTest extends TestCase
{
    // redirect to the admin page
    public function testMain(): void
    {
        \Roles::shouldReceive('hasRole')->times(0);

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/');

        $response
            ->assertRedirect('/admin')
            ->assertStatus(302);
    }

    public function testAdmin(): void
    {
        \Roles::shouldReceive('hasRole')
            ->times(9)
            ->andReturnUsing(static function ($user, $role) {
                return true;
            });

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/admin');

        $response->assertStatus(200);
    }

    public function testDoctor(): void
    {
        \Roles::shouldReceive('hasRole')
            ->times(5)
            ->andReturnUsing(function ($user, $role) {
                return false;
            });

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/admin');

        $response->assertStatus(403);
    }

    public function testDirector(): void
    {
        \Roles::shouldReceive('hasRole')
            ->times(5)
            ->andReturnUsing(function ($user, $role) {
                return false;
            });

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/admin');

        $response->assertStatus(403);
    }
}
