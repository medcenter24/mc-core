<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
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

namespace medcenter24\mcCore\Tests\Feature\Api\Director;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use medcenter24\mcCore\App\DoctorSurvey;
use medcenter24\mcCore\Tests\Feature\Api\JwtHeaders;
use medcenter24\mcCore\Tests\Feature\Api\LoggedUser;
use medcenter24\mcCore\Tests\TestCase;

class SurveysControllerTest extends TestCase
{
    use DatabaseMigrations;
    use JwtHeaders;
    use LoggedUser;

    private const URI = 'api/director/surveys';

    public function testStore(): void
    {
        $description = 'description';
        $title = 'PHPUnit Survey';
        $response = $this->json('POST', self::URI, [
            'title' => $title,
            'description' => $description,
            'diseaseId' => 1,
        ], $this->headers($this->getUser()));
        $response->assertStatus(201);
        $response->assertJson([
            'id' => 1,
            'title' => $title,
            'description' => $description,
            'diseaseId' => 1,
            'type' => 'director',
            'status' => 'active',
            'diseaseTitle' => '',
        ]);
    }

    public function testUpdate(): void
    {
        /** @var DoctorSurvey $survey */
        $survey = DoctorSurvey::create([
            'title' => 'PHPUnitTest Survey',
            'description' => 'description',
        ]);
        $response = $this->put(self::URI . '/' . $survey->getAttribute('id'), [
            'title' => 'PHPUnitTest Changed',
        ], $this->headers($this->getUser()));
        $response->assertStatus(202);
        $response->assertJson([
            'id' => 1,
            'title' => 'PHPUnitTest Changed',
            'description' => 'description',
            'diseaseId' => 0,
            'type' => 'system',
            'status' => 'active',
            'diseaseTitle' => '',
        ]);
    }
}
