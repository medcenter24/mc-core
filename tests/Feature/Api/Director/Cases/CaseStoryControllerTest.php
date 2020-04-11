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

use medcenter24\mcCore\App\Services\Entity\CaseAccidentService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\TestCase;
use ScenariosTableSeeder;

class CaseStoryControllerTest extends TestCase
{
    use DirectorTestTraitApi;

    /**
     * @var CaseAccidentService
     */
    private $caseAccidentService;

    protected function setUp(): void
    {
        parent::setUp();
        (new ScenariosTableSeeder())->run();
        $this->caseAccidentService = new CaseAccidentService();
    }


    public function testStory(): void
    {
        $accident = $this->caseAccidentService->create();
        $response = $this->sendGet('/api/director/cases/' . $accident->id . '/scenario');
        $response->assertStatus(200);
        $response->assertJson(array (
            'data' =>
                array (
                    0 =>
                        array (
                            'id' => 1,
                            'tag' => 'medcenter24\\mcCore\\App\\Entity\\DoctorAccident',
                            'order' => 1,
                            'mode' => 'step',
                            'accidentStatusId' => 1,
                            'status' => 'current',
                            'title' => 'new',
                        ),
                    1 =>
                        array (
                            'id' => 2,
                            'tag' => 'medcenter24\\mcCore\\App\\Entity\\DoctorAccident',
                            'order' => 2,
                            'mode' => 'step',
                            'accidentStatusId' => 2,
                            'status' => '',
                            'title' => 'assigned',
                        ),
                    2 =>
                        array (
                            'id' => 3,
                            'tag' => 'medcenter24\\mcCore\\App\\Entity\\DoctorAccident',
                            'order' => 3,
                            'mode' => 'step',
                            'accidentStatusId' => 3,
                            'status' => '',
                            'title' => 'in_progress',
                        ),
                    3 =>
                        array (
                            'id' => 4,
                            'tag' => 'medcenter24\\mcCore\\App\\Entity\\DoctorAccident',
                            'order' => 4,
                            'mode' => 'step',
                            'accidentStatusId' => 4,
                            'status' => '',
                            'title' => 'sent',
                        ),
                    4 =>
                        array (
                            'id' => 5,
                            'tag' => 'medcenter24\\mcCore\\App\\Entity\\DoctorAccident',
                            'order' => 5,
                            'mode' => 'step',
                            'accidentStatusId' => 5,
                            'status' => '',
                            'title' => 'paid',
                        ),
                    5 =>
                        array (
                            'id' => 7,
                            'tag' => 'medcenter24\\mcCore\\App\\Entity\\DoctorAccident',
                            'order' => 7,
                            'mode' => 'step',
                            'accidentStatusId' => 7,
                            'status' => '',
                            'title' => 'closed',
                        ),
                ),
        ));
    }
}
