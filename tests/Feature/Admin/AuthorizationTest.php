<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Admin;


use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();
        $_SESSION = [];
    }

    public function testUnauthorizedRedirect()
    {
        $response = $this->get('/admin');
        $response->assertRedirect('login');
    }

    public function testWrongCredentialsAuthorization()
    {
        $response = $this->post('login', [
            'email' => 'mail@example.com',
            'password' => '234234secureing...',
        ]);
        if ($response->getStatusCode() != 302) {
            var_dump($response->getContent());
        }
        $response->assertRedirect('')->assertSessionHas('_token', session()->get('_token'));
        $this->get('admin')->assertRedirect('login');
        $response->assertStatus(302);
    }

    public function testAuthorization()
    {
        $mail = 'mail@example.com';
        $passwd = 'secure';
        $this->user = factory(User::class)->create([
            'email' => $mail,
            'password' => bcrypt($passwd),
        ]);

        $response = $this->post('login', [
            'email' => $mail,
            'password' => $passwd,
        ]);

        $response->assertRedirect('')->assertSessionHas('_token', session()->get('_token'));
        $response->assertStatus(302);

        $r2 = $this->get('admin'); //->assertRedirect('login');
        $r2->assertStatus(403);
        $r2->assertSee('Access denied');
    }

    public function testAdminsAuthorization()
    {
        $mail = 'mail@example.com';
        $passwd = 'secure';
        /** @var User user */
        $this->user = factory(User::class)->create([
            'email' => $mail,
            'password' => bcrypt($passwd),
        ]);

        \Roles::shouldReceive('hasRole')
            ->times(3)
            ->andReturnUsing(function ($user, $role) {
                return true;
            });

        $response = $this->post('login', [
            'email' => $mail,
            'password' => $passwd,
        ]);

        $response->assertRedirect('')->assertSessionHas('_token', session()->get('_token'));
        $response->assertStatus(302);

        $r2 = $this->get('admin'); //->assertRedirect('login');
        $r2->assertStatus(200);
        $r2->assertSee('MyDoctor 24');
    }
}
