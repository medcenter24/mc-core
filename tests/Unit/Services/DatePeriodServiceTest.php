<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Services;


use App\Services\DatePeriodService;
use Tests\TestCase;

class DatePeriodServiceTest extends TestCase
{

    /**
     * @var DatePeriodService
     */
    private $service;

    protected function setUp()
    {
        parent::setUp();
        $this->service = new DatePeriodService();
    }

    public function periodsDataProvider()
    {
        return [
            ['sun 00:00'],
            ['23:40'],
            ['mon 23:59'],
            [' mon 23:40'],
        ];
    }

    /**
     * @param $period
     * @dataProvider periodsDataProvider
     */
    public function testIsPeriod(string $period)
    {
        self::assertTrue($this->service->isPeriod($period));
    }

    public function notAPeriodsDataProvider()
    {
        return [
            ['suns 00:00'],
            [' _mon 23:40'],
            ['mon 23a:59p'],
            ['mon 23a:59'],
            ['55:55'],
            ['_1:11'],
        ];
    }

    /**
     * @param $period
     * @dataProvider notAPeriodsDataProvider
     */
    public function testIsNotPeriod(string $period)
    {
        self::assertNotTrue($this->service->isPeriod($period));
    }
}
