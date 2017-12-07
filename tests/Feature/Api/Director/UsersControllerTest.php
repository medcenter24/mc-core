<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Api\Director;


use App\Services\LogoService;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Api\JwtHeaders;
use Tests\Feature\Api\LoggedUser;
use Tests\TestCase;

class UsersControllerTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    public function testOptions()
    {
        $user = factory(User::class)->create(['password' => bcrypt('foo')]);

        $response = $this->json('OPTIONS', '/api/director/users/' . $user->id . '/photo', [], $this->headers($user));
        $response->assertStatus(200)
            ->assertHeader('Allow', 'GET,HEAD,POST,DELETE');
    }

    public function testUpdatePhoto()
    {
        Storage::fake(LogoService::DISC);

        $user = factory(User::class)->create(['password' => bcrypt('foo')]);
        $response = $this->json('POST', '/api/director/users/' . $user->id . '/photo',
            ['file' => UploadedFile::fake()->image('photo.jpg', 100, 100)]
            , $this->headers($this->getUser())
        );

        $response->assertStatus(202);
    }
}
