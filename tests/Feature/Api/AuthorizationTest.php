<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
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
        $response->assertStatus(400);
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
