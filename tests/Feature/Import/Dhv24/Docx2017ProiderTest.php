<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Import\Dhv24;

use App\Services\Import\Dhv24\Dhv24Docx2017Provider;
use Tests\SamplePath;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class Docx2017ProiderTest extends TestCase
{
    use DatabaseMigrations;
    use SamplePath;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testImport()
    {
        $provider = new Dhv24Docx2017Provider();
        $provider->load($this->getSampleFile('t1.docx'));
        self::assertTrue($provider->check(), 'File content is checked');
        $accident = $provider->import();
        dd($accident);
    }
}
