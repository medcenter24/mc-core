<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Api\Director;

use App\Accident;
use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Api\JwtHeaders;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CasesImportTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;

    public function testOptions()
    {
        $user = factory(User::class)->create(['password' => bcrypt('foo')]);

        $response = $this->json('OPTIONS', '/api/director/cases/importer', [], $this->headers($user));
        $response->assertStatus(200)
            ->assertHeader('Allow', 'POST, PUT, OPTIONS');
    }

    public function testUpload()
    {
        Storage::fake('imports');

        $response = $this->json('POST', '/api/director/cases/importer', [
            'case' => UploadedFile::fake()->create('imported.docx', 100)
        ]);

        // Assert the file was stored...
        Storage::disk('imports')->assertExists('imported.docx');

        // Assert a file does not exist...
        Storage::disk('imports')->assertMissing('doesnotimported.docx');
    }

    /**
     * Place import to the storage and run the import
     */
    public function testImport()
    {
    }

}
