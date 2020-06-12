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

declare(strict_types = 1);

namespace medcenter24\mcCore\Tests\Feature\Api\Director\Cases\CaseController;

use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\AccidentStatusService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\TestCase;

class CasesControllerSearchActionTest extends TestCase
{
    use DirectorTestTraitApi;

    public function testSearch(): void
    {
        factory(Accident::class, 7)->create();
        $response = $this->sendPost('/api/director/cases/search', []);

        $response->assertStatus(200)->assertJson([
            'data' => [
                ['id' => 1]
            ],
            'meta' => [
                'pagination' => [
                    'total' => 7,
                    'count' => 7,
                    'per_page' => 15,
                    'current_page' => 1,
                    'total_pages' => 1,
                    'links' => [],
                ]
            ]
        ]);
    }

    /**
     * @throws InconsistentDataException
     */
    public function testSearchClosed(): void
    {
        $accidentService = new AccidentService();
        $accidentStatusService = new AccidentStatusService();
        // I can't create closed Accident at all
        /** @var Accident $accident */
        $accident = $accidentService->create([
            'accident_status_id' => $accidentStatusService->getNewStatus()->getAttribute('id'),
        ]);

        $accidentService->setStatus($accident, $accidentStatusService->getClosedStatus());

        $response = $this->sendPost('/api/director/cases/search', []);

        $response->assertStatus(200)->assertJson([
            'data' => [
                ['id' => 1]
            ],
            'meta' => [
                'pagination' => [
                    'total' => 1,
                    'count' => 1,
                    'per_page' => 15,
                    'current_page' => 1,
                    'total_pages' => 1,
                    'links' => [],
                ]
            ]
        ]);
    }
}
