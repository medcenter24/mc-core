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
 * Copyright (c) 2020 (original work) MedCenter24.com;
 */

declare(strict_types = 1);

namespace medcenter24\mcCore\Tests\Feature\Api\Director\Cases;

use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Services\Entity\CaseAccidentService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\TestCase;

class CaseCommentControllerTest extends TestCase
{
    use DirectorTestTraitApi;

    /**
     * @var CaseAccidentService
     */
    private $caseAccidentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->caseAccidentService = new CaseAccidentService();
    }

    /**
     * @throws InconsistentDataException
     */
    public function testEmptyCommentsList(): void
    {
        $accident = $this->caseAccidentService->create();
        $response = $this->sendGet('/api/director/cases/' . $accident->id . '/comments');
        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [],
        ]);
    }

    /**
     * @throws InconsistentDataException
     */
    public function testAddComment(): void
    {
        $accident = $this->caseAccidentService->create();
        $response = $this->sendGet('/api/director/cases/' . $accident->id . '/comments');
        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [],
        ]);

        $addResponse = $this->sendPost('/api/director/cases/' . $accident->id . '/comments', [
            'text' => 'PHPUnit test',
        ]);
        $addResponse->assertStatus(201);
        $addResponse->assertJson(['data' => [
            'userName' => 'PHPUnit',
            'userThumb' => '',
            'userId' => 1,
            'id' => 1,
            'body' => 'PHPUnit test',
        ]]);

        $response = $this->sendGet('/api/director/cases/' . $accident->id . '/comments');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'body' => 'PHPUnit test',
                    'id' => 1,
                    'userId' => 1,
                    'userName' => 'PHPUnit',
                    'userThumb' => '',
                ]
            ],
        ]);
    }
}
