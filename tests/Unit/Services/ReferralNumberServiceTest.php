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

namespace Tests\Unit\Services;

use medcenter24\mcCore\App\DoctorAccident;
use medcenter24\mcCore\App\HospitalAccident;
use medcenter24\mcCore\App\Services\AccidentService;
use medcenter24\mcCore\App\Services\ReferralNumberService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Unit\fakes\AccidentFake;

class ReferralNumberServiceTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * @var ReferralNumberService
     */
    private $service;

    /**
     * @throws \ReflectionException
     */
    protected function setUp(): void
    {
        parent::setUp();
        /** @var AccidentService $accidentService */
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
    public function testGenerateDoctorsKey()
    {
        $params = [
            'ref_num' => '',
            'created_by' => 1,
            'patient_id' => 1,
            'accident_type_id' => 1,
            'accident_status_id' => 1,
            'assistant_id' => 1,
            'caseable_id' => 1,
            'caseable_type' => DoctorAccident::class,
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
        self::assertEquals('T0003-'
            . Carbon::now()->format('dmy')
            . '-'
            . 'DOC'
            . $this->service->getTimesOfDayCode(Carbon::now()), $this->service->generate(AccidentFake::make($params, $additionalParams)));
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGenerateHospitalsKey()
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
        self::assertEquals('T0003-'
            . Carbon::now()->format('dmy')
            . '-'
            . 'HOSPITAL'
            . $this->service->getTimesOfDayCode(Carbon::now()), $this->service->generate(AccidentFake::make($params, $additionalParams)));
    }
}
