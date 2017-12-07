<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Api\Director;

use App\Services\CaseImporterService;
use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Api\JwtHeaders;
use Tests\Feature\Api\LoggedUser;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CasesImportTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    public function testOptions()
    {
        $user = factory(User::class)->create(['password' => bcrypt('foo')]);

        $response = $this->json('OPTIONS', '/api/director/cases/importer', [], $this->headers($user));
        $response->assertStatus(200)
            ->assertHeader('Allow', 'GET,HEAD,POST,PUT,PATCH,DELETE');
    }

    public function testUpload()
    {
        Storage::fake(CaseImporterService::DISC_IMPORTS);

        $response = $this->json('POST', '/api/director/cases/importer',
            [[UploadedFile::fake()->create('imported.docx', 100)]]
            , $this->headers($this->getUser()));

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['path', 'name']]]);

        $data = $response->json();

        // Assert the file was stored...
        self::assertEquals('imported.docx', $data['data'][0]['name']);
        Storage::disk('imports')->assertExists(str_replace('imports/', '', $data['data'][0]['path']));

        self::assertCount(1, $this->getUser()->uploads()->get());
        self::assertEquals($data['data'][0]['path'], $this->getUser()->uploads()->first()->path);
    }
}
