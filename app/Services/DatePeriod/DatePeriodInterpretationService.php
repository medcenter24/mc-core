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

namespace App\Services\DatePeriod;

use App\DatePeriod;
use App\DatePeriodInterpretation;
use Carbon\Carbon;

/**
 * Converting GUIs periods to the storing format to make possibility to use it in the DB with SQL
 * Class DatePeriodInterpretationService
 * @package App\Services
 */
class DatePeriodInterpretationService
{
    /**
     * @var DatePeriodService
     */
    private $datePeriodService;

    public function __construct(DatePeriodService $datePeriodService)
    {
        $this->datePeriodService = $datePeriodService;
    }

    /**
     * @param DatePeriod $datePeriod
     * @return array
     * @throws \App\Exceptions\InconsistentDataException
     */
    public function interpret(DatePeriod $datePeriod): array
    {
        $from = $this->datePeriodService->parsePeriod($datePeriod->from);
        $to = $this->datePeriodService->parsePeriod($datePeriod->to);

        $time = explode(':', $from[DatePeriodService::TIME]);
        $timeFrom = Carbon::createFromTime($time[0], $time[1]);

        $time = explode(':', $to[DatePeriodService::TIME]);
        $timeTo = Carbon::createFromTime($time[0], $time[1]);

        $result = [];
        $this->addFirstDate($from, $result);
        if ($from[DatePeriodService::DOW] !== $to[DatePeriodService::DOW]) {
            // adding all the days between dates
            $this->addBetweenDates($from, $to, $result);
        } else {
            // the same day of week
            if ($timeFrom->greaterThanOrEqualTo($timeTo)) { // timeFrom >= $timeTo
                // adding all the days between dates
                $this->addBetweenDates($from, $to, $result);
            }
        }
        $this->addLastDate($to, $result);

        return $result;
    }

    private function addFirstDate($from, &$res = [])
    {
        $res[] = [$from['dow'], $from['time'], '23:59'];
    }

    private function addBetweenDates($from, $to, &$result)
    {
        $days = $this->datePeriodService->getDow();
        $currentDay = array_search($from['dow'], $days);
        $currentDay = $this->getNextDay($currentDay);
        $lastDay = array_search($to['dow'], $days);
        while ($currentDay != $lastDay) {
            // add this day
            $result[] = [$days[$currentDay], '00:00', '23:59'];
            $currentDay = $this->getNextDay($currentDay);
        }
    }

    private function getNextDay($day = 0)
    {
        return ++$day > 6 ? 0 : $day;
    }

    private function addLastDate($to, &$result)
    {
        if (count($result) == 1 && $result[0][0] === $to['dow']) {
            // nothing was added, that means that we have 1 day only
            $result = [[$result[0][0], $result[0][1], $to['time']]];
        } else {
            // there are many days, we need to add last one
            $result[] = [$to['dow'], '00:00', $to['time']];
        }
    }

    /**
     * Updating interpreted data
     * @param DatePeriod $datePeriod
     * @throws \App\Exceptions\InconsistentDataException
     */
    public function update(DatePeriod $datePeriod)
    {
        // delete old data
        DatePeriodInterpretation::where('date_period_id', $datePeriod->id)->delete();
        $days = $this->interpret($datePeriod);
        $data = [];
        foreach ($days as $day) {
            $data[] = [
                'date_period_id' => (int)$datePeriod->id,
                'day_of_week' => (int)$day[0],
                'from' => $day[1],
                'to' => $day[2],
            ];
        }
        DatePeriodInterpretation::insert($data);
    }
}
