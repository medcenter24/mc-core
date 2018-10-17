<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Services;


use App\Services\DatePeriodInterpretationService;
use App\Services\DatePeriodService;
use Tests\TestCase;
use Tests\Unit\fakes\DatePeriodFake;

class DatePeriodInterpretationServiceTest extends TestCase
{

    /**
     * @var DatePeriodInterpretationService
     */
    private $service;

    protected function setUp()
    {
        parent::setUp();
        $this->service = new DatePeriodInterpretationService(new DatePeriodService());
    }

    public function periodsDataProvider()
    {
        return [
            [ ['from' => 'sun 00:00', 'to' => 'sun 00:01'], [['sun', '00:00', '00:01']] ], // 1 sec
            [ ['from' => 'sun 00:01', 'to' => 'sun 00:00'], [
                ['sun', '00:01', '23:59'],
                ['mon', '00:00', '23:59'],
                ['tues', '00:00', '23:59'],
                ['wed', '00:00', '23:59'],
                ['thurs', '00:00', '23:59'],
                ['fri', '00:00', '23:59'],
                ['sat', '00:00', '23:59'],
                ['sun', '00:00', '00:00'], // edge case when hours are equal
            ] ], // 7 days
            [ ['from' => 'sun 00:02', 'to' => 'sun 00:00'], [
                ['sun', '00:02', '23:59'],
                ['mon', '00:00', '23:59'],
                ['tues', '00:00', '23:59'],
                ['wed', '00:00', '23:59'],
                ['thurs', '00:00', '23:59'],
                ['fri', '00:00', '23:59'],
                ['sat', '00:00', '23:59'],
                ['sun', '00:00', '00:00'], // edge case when hours are equal
            ] ], // 7 days - 1sec
            [ ['from' => 'sun 00:00', 'to' => 'sun 00:00'], [
                ['sun', '00:00', '23:59'],
                ['mon', '00:00', '23:59'],
                ['tues', '00:00', '23:59'],
                ['wed', '00:00', '23:59'],
                ['thurs', '00:00', '23:59'],
                ['fri', '00:00', '23:59'],
                ['sat', '00:00', '23:59'],
                ['sun', '00:00', '00:00'],
            ] ], // 7 days
            [ ['from' => 'wed 12:44', 'to' => 'thurs 01:12'], [
                ['wed', '12:44', '23:59'],
                ['thurs', '00:00', '01:12'],
            ] ], //
            [ ['from' => 'wed 12:44', 'to' => 'fri 01:12'], [
                ['wed', '12:44', '23:59'],
                ['thurs', '00:00', '23:59'],
                ['fri', '00:00', '01:12'],
            ] ], //
        ];
    }

    /**
     * @dataProvider periodsDataProvider
     *
     * @param array $period
     * @param array $expected
     * @throws \App\Exceptions\InconsistentDataException
     */
    public function testInterpreter(array $period, array $expected)
    {
        self::assertEquals($expected, $this->service->interpret(DatePeriodFake::make($period)));
    }
}
