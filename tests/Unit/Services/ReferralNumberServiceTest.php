<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Services;

use App\Services\AccidentService;
use App\Services\ReferralNumberService;
use Carbon\Carbon;
use Tests\TestCase;
use Tests\Unit\fakes\AccidentFake;

class ReferralNumberServiceTest extends TestCase
{
    /**
     * @var ReferralNumberService
     */
    private $service;

    protected function setUp()
    {
        parent::setUp();
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
    public function testGenerate()
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
            'discount_id' => 1,
        ];
        $additionalParams = [
            'assistant' => ['ref_key' => 'T', 'id'=>1],
            'doctorAccident' => [],
            'doctor' => [
                'ref_key' => 'DOC',
                'city_id' => 1,
            ],
        ];
        self::assertEquals('T0003-' . Carbon::now()->format('dmy') . '-DDOC', $this->service->generate(AccidentFake::make($params, $additionalParams)));
    }
}
