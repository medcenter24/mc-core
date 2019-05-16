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

namespace medcenter24\mcCore\Tests\Feature\Api\Director;


use medcenter24\mcCore\App\Services\LogoService;
use medcenter24\mcCore\App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use medcenter24\mcCore\Tests\Feature\Api\JwtHeaders;
use medcenter24\mcCore\Tests\Feature\Api\LoggedUser;
use medcenter24\mcCore\Tests\TestCase;

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
            ->assertHeader('Allow', 'POST,DELETE');
    }

    public function testUpdatePhoto()
    {
        Storage::fake(LogoService::DISC);

        $user = factory(User::class)->create(['password' => bcrypt('foo')]);
        $response = $this->json('POST', '/api/director/users/' . $user->id . '/photo',
            ['file' => UploadedFile::fake()->image('photo.jpg', 100, 100)]
            , $this->headers($this->getUser())
        );
        $response->assertStatus(200);
    }
}
