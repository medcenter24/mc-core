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

use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Entity\HospitalAccident;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\CaseAccidentService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;

class CaseCaseableControllerTest extends DirectorTestTraitApi
{
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
    public function testHospitalCaseable(): void
    {
        $accident = $this->caseAccidentService->create([
            CaseAccidentService::PROPERTY_ACCIDENT => [
                AccidentService::FIELD_CASEABLE_TYPE => HospitalAccident::class,
            ],
        ]);
        $response = $this->sendGet('/api/director/cases/' . $accident->getKey() . '/hospitalcase');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => 1,
                'accidentId' => 1,
                'assistantGuaranteeId' => 0,
                'assistantInvoiceId' => 0,
                'hospitalGuaranteeId' => 0,
                'hospitalId' => 0,
                'hospitalInvoiceId' => 0,
            ]
        ]);
    }

    /**
     * @throws InconsistentDataException
     */
    public function testDoctorCaseable(): void
    {
        $accident = $this->caseAccidentService->create([
            CaseAccidentService::PROPERTY_ACCIDENT => [
                AccidentService::FIELD_CASEABLE_TYPE => DoctorAccident::class,
            ],
        ]);
        $response = $this->sendGet('/api/director/cases/' . $accident->getKey() . '/doctorcase');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => 1,
                'cityId' => 0,
                'doctorId' => 0,
                'investigation' => '',
                'visitTime' => '',
                'recommendation' => '',
            ]
        ]);
    }

    /**
     * @throws InconsistentDataException
     */
    public function testWrongDocExpectation(): void
    {
        $accident = $this->caseAccidentService->create([
            CaseAccidentService::PROPERTY_ACCIDENT => [
                AccidentService::FIELD_CASEABLE_TYPE => HospitalAccident::class,
            ],
        ]);
        $response = $this->sendGet('/api/director/cases/' . $accident->getKey() . '/doctorcase');
        $response->assertStatus(400);
        $response->assertJson(['message' => 'Doctor case expected']);
    }

    /**
     * @throws InconsistentDataException
     */
    public function testWrongHospExpectation(): void
    {
        $accident = $this->caseAccidentService->create([
            CaseAccidentService::PROPERTY_ACCIDENT => [
                AccidentService::FIELD_CASEABLE_TYPE => DoctorAccident::class,
            ],
        ]);
        $response = $this->sendGet('/api/director/cases/' . $accident->getKey() . '/hospitalcase');
        $response->assertStatus(400);
        $response->assertJson(['message' => 'Hospital case expected']);
    }
}
