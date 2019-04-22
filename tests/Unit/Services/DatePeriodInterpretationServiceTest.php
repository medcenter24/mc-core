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


use medcenter24\mcCore\App\Services\DatePeriod\DatePeriodInterpretationService;
use medcenter24\mcCore\App\Services\DatePeriod\DatePeriodService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Unit\fakes\DatePeriodFake;

class DatePeriodInterpretationServiceTest extends TestCase
{

    use DatabaseMigrations;

    /**
     * @var DatePeriodInterpretationService
     */
    private $service;

    protected function setUp(): void
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
