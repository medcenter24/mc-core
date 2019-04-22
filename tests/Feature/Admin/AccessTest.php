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

namespace medcenter24\mcCore\Tests\Feature\Admin;

use medcenter24\mcCore\App\User;
use medcenter24\mcCore\Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AccessTest extends TestCase
{
    public function testMain(): void
    {
        \Roles::shouldReceive('hasRole')->times(0);

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/');

        $response->assertStatus(200);
    }

    public function testAdmin(): void
    {
        \Roles::shouldReceive('hasRole')
            ->times(3)
            ->andReturnUsing(function ($user, $role) {
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
            ->times(1)
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
            ->times(1)
            ->andReturnUsing(function ($user, $role) {
                return false;
            });

        $response = $this
            ->actingAs(factory(User::class)->make())
            ->get('/admin');

        $response->assertStatus(403);
    }
}
