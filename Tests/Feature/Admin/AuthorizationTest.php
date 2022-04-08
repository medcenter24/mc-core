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

use medcenter24\mcCore\App\Entity\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Testing\TestResponse;
use medcenter24\mcCore\App\Support\Facades\Roles;
use medcenter24\mcCore\Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->startSession();
    }

    public function testUnauthorizedRedirect(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('login');
    }

    public function testWrongCredentialsAuthorization(): void
    {
        $response = $this->post('login', [
            'email' => 'mail@example.com',
            'password' => '234234secureing...',
            '_token' => csrf_token(),
        ]);
        if ($response->getStatusCode() !== 302) {
            // why am I here?
            $this->assertTrue(false, 'I have to get correct Status Code 302 but '
                . $response->getStatusCode());
        }
        $response->assertRedirect('')->assertSessionHas('_token', session()->get('_token'));
        $this->get('admin')->assertRedirect('login');
        $response->assertStatus(302);
    }

    public function testAuthorization(): void
    {
        $mail = 'mail@example.com';
        $passwd = 'secure';
        $this->user = User::factory()->create([
            'email' => $mail,
            'password' => bcrypt($passwd),
        ]);

        $response = $this->post('login', [
            'email' => $mail,
            'password' => $passwd,
            '_token' => csrf_token(),
        ]);

        $response->assertRedirect('')->assertSessionHas('_token', session()->get('_token'));
        $response->assertStatus(302);

        $r2 = $this->get('admin');
        $r2->assertStatus(403);
        $r2->assertSee('Forbidden');
    }

    public function testAdminsAuthorization(): void
    {
        $mail = 'mail@example.com';
        $passwd = 'secure';
        /** @var User user */
        $this->user = User::factory()->create([
            'email' => $mail,
            'password' => bcrypt($passwd),
        ]);

        Roles::shouldReceive('hasRole')
            ->times(1)
            ->andReturnUsing(function ($user, $role) {
                return true;
            });

        $response = $this->post('login', [
            'email' => $mail,
            'password' => $passwd,
            '_token' => csrf_token(),
        ]);

        $response->assertRedirect('')->assertSessionHas('_token', session()->get('_token'));
        $response->assertStatus(302);

        $r2 = $this->get('admin');
        $r2->assertStatus(302);
        $r2->assertRedirect('/admin/users');
    }
}
