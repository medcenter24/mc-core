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

use medcenter24\mcCore\App\Entity\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use medcenter24\mcCore\App\Support\Facades\Roles;
use medcenter24\mcCore\Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    public function testAuthorizationWithoutJwtHeaders(): void
    {
        $response = $this->json('POST', '/api/authenticate');
        $response->assertStatus(401);
    }

    public function testWrongCredentialsAuthorization(): void
    {
        $response = $this->json('POST', '/api/authenticate', [
            'email' => 'mail@example.com',
            'password' => 'secure',
        ], $this->headers());
        $response->assertStatus(401);
    }

    public function testAuthorization(): void
    {
        Roles::shouldReceive('hasRole')
            ->andReturnUsing(function () {
                return true;
            });
        $mail = 'mail@example.com';
        $passwd = 'secure';
        $this->user = User::factory()->create([
            'email' => $mail,
            'password' => bcrypt($passwd),
        ]);

        $response = $this->json('POST', '/api/authenticate', [
            'email' => $mail,
            'password' => $passwd,
        ], $this->headers());

        $response->assertStatus(202);
    }

    public function testLogout(): void
    {
        $response = $this->post('/api/logout', $this->headers($this->getUser()));
        $response->assertStatus(401);
    }

    public function testToken(): void
    {
        $response = $this->get('/api/token', $this->headers($this->getUser()));
        $response->assertStatus(202);
        $response->assertJson([
            'token_type' => 'bearer',
            'expires_in' => 86400,
            'lang' => '',
            'thumb' => '',
        ], true);
        $token = $response->assertJsonStructure([
            'token_type',
            'expires_in',
            'lang',
            'thumb',
            'access_token',
        ]);
        self::assertNotEmpty($token);
    }

    public function testGetUser(): void
    {
        $response = $this->get('/api/user', $this->headers($this->getUser()));
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => [
            'id', 'name', 'email', 'phone', 'lang', 'thumb200', 'thumb45', 'timezone'
        ]]);
    }
}
