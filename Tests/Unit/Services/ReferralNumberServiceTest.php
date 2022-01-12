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
declare(strict_types=1);

namespace medcenter24\mcCore\Tests\Unit\Services;

use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\Assistant;
use medcenter24\mcCore\App\Entity\Doctor;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Entity\Hospital;
use medcenter24\mcCore\App\Entity\HospitalAccident;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\ReferralNumberService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use medcenter24\mcCore\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ReferralNumberServiceTest extends TestCase
{
    use DatabaseMigrations;

    private ReferralNumberService $service;

    protected function setUp(): void
    {
        parent::setUp();
        /** @var AccidentService|MockObject $accidentService */
        $accidentService = $this->createMock(AccidentService::class);
        $accidentService->method('getCountByAssistance')
            ->willReturn(3);

        $this->service = new ReferralNumberService($accidentService);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGenerateDoctorsKey(): void
    {
        $params = [
            'ref_num' => '',
            'created_by' => 1,
            'patient_id' => 1,
            'accident_type_id' => 1,
            'accident_status_id' => 1,
            'assistant_id' => 1,
            'caseable_id' => 1,
            'form_report_id' => 1,
            'city_id' => 1,
        ];
        $additionalParams = [
            'assistant' => ['ref_key' => 'T', 'id'=>1],
            'doctorAccident' => [],
            'doctor' => [
                'ref_key' => 'DOC',
                'city_id' => 1,
            ],
        ];
        /** @var Accident $accident */
        $accident = Accident::factory()->create($params + [
            'assistant_id' => Assistant::factory()->create($additionalParams['assistant']),
            'caseable_type' => DoctorAccident::class,
            'caseable_id' => DoctorAccident::factory()->create([
                'doctor_id' => Doctor::factory()->create($additionalParams['doctor']),
            ])
        ]);
        self::assertEquals('T0003-'
                . Carbon::now()->format('dmy')
                . '-'
                . 'DOC'
                . $this->service->getTimesOfDayCode(Carbon::now())
            , $this->service->generate($accident)
        );
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGenerateHospitalsKey(): void
    {
        $params = [
            'ref_num' => '',
            'created_by' => 1,
            'patient_id' => 1,
            'accident_type_id' => 1,
            'accident_status_id' => 1,
            'assistant_id' => 1,
            'form_report_id' => 1,
            'city_id' => 1,
        ];
        $additionalParams = [
            'assistant' => ['ref_key' => 'T', 'id' => 1],
            'hospitalAccident' => [],
            'hospital' => [
                'ref_key' => 'HOSPITAL',
            ],
        ];

        /** @var Accident $accident */
        $accident = Accident::factory()->create($params + [
            'assistant_id' => Assistant::factory()->create($additionalParams['assistant']),
            'caseable_type' => HospitalAccident::class,
            'caseable_id' => HospitalAccident::factory()->create([
                'hospital_id' => Hospital::factory()->create($additionalParams['hospital']),
            ])
        ]);

        self::assertEquals('T0003-'
                . Carbon::now()->format('dmy')
                . '-'
                . 'HOSPITAL'
                . $this->service->getTimesOfDayCode(Carbon::now())
            , $this->service->generate($accident)
        );
    }
}
