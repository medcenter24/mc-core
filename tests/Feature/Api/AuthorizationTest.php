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

namespace Tests\Feature\Api;


use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;

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

    public function testAuthorization()
    {
        \Roles::shouldReceive('hasRole')
            ->andReturnUsing(function () {
                return true;
            });
        $mail = 'mail@example.com';
        $passwd = 'secure';
        $this->user = factory(User::class)->create([
            'email' => $mail,
            'password' => bcrypt($passwd),
        ]);

        $response = $this->json('POST', '/api/authenticate', [
            'email' => $mail,
            'password' => $passwd,
        ], $this->headers());

        if ($response->getStatusCode() != 200) {
            var_dump($response->getContent());
        }
        $response->assertStatus(200);
    }
}
