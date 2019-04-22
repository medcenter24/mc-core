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


use medcenter24\mcCore\App\Services\DatePeriod\DatePeriodService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class DatePeriodServiceTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var DatePeriodService
     */
    private $service;

    protected function setUp(): void
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

    public function testSave()
    {
        $data = [
            'title' => 'test',
            'from' => 'sun 12:21',
            'to' => 'mon 22:15',
        ];
        $datePeriod = $this->service->save($data);
        $data['id'] = 1;
        self::assertSame($data, $datePeriod->toArray());
        self::assertSame([[
            'date_period_id' => '1',
            'day_of_week' => 'sun',
            'from' => '12:21',
            'to' => '23:59',
        ], [
            'date_period_id' => '1',
            'day_of_week' => 'mon',
            'from' => '00:00',
            'to' => '22:15',
        ]], $datePeriod->interpretation->toArray());
    }
}
