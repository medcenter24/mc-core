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

namespace medcenter24\mcCore\App\Services\Entity;

use Illuminate\Support\Carbon;
use medcenter24\mcCore\App\Entity\DatePeriod;
use medcenter24\mcCore\App\Entity\DatePeriodInterpretation;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;

class DatePeriodInterpretationService extends AbstractModelService
{

    public const FIELD_DATE_PERIOD_ID = 'date_period_id';
    public const FIELD_DAY_OF_WEEK = 'day_of_week';
    public const FIELD_FROM = 'from';
    public const FIELD_TO = 'to';

    public const FILLABLE = [
        self::FIELD_DATE_PERIOD_ID,
        self::FIELD_DAY_OF_WEEK,
        self::FIELD_FROM,
        self::FIELD_TO,
    ];

    public const VISIBLE = [
        self::FIELD_ID,
        self::FIELD_DATE_PERIOD_ID,
        self::FIELD_DAY_OF_WEEK,
        self::FIELD_FROM,
        self::FIELD_TO,
    ];

    public const UPDATABLE = [
        self::FIELD_DATE_PERIOD_ID,
        self::FIELD_DAY_OF_WEEK,
        self::FIELD_FROM,
        self::FIELD_TO,
    ];

    /**
     * @inheritDoc
     */
    protected function getClassName(): string
    {
        return DatePeriodInterpretation::class;
    }

    /**
     * @inheritDoc
     */
    protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_DATE_PERIOD_ID => 0,
            self::FIELD_DAY_OF_WEEK => '',
            self::FIELD_FROM => '',
            self::FIELD_TO => '',
        ];
    }

    /**
     * @param DatePeriod $datePeriod
     * @return array
     * @throws InconsistentDataException
     */
    public function interpret(DatePeriod $datePeriod): array
    {
        $from = $this->getServiceLocator()->get(DatePeriodService::class)->parsePeriod($datePeriod->from);
        $to = $this->getServiceLocator()->get(DatePeriodService::class)->parsePeriod($datePeriod->to);

        $time = explode(':', $from[DatePeriodService::TIME]);
        $timeFrom = Carbon::createFromTime($time[0], $time[1]);

        $time = explode(':', $to[DatePeriodService::TIME]);
        $timeTo = Carbon::createFromTime($time[0], $time[1]);

        $result = [];
        $this->addFirstDate($from, $result);
        if ($from[DatePeriodService::DOW] !== $to[DatePeriodService::DOW]) {
            // adding all the days between dates
            $this->addBetweenDates($from, $to, $result);
        } elseif ($timeFrom->greaterThanOrEqualTo($timeTo)) { // the same day of week
            // timeFrom >= $timeTo
            // adding all the days between dates
            $this->addBetweenDates($from, $to, $result);
        }
        $this->addLastDate($to, $result);

        return $result;
    }

    /**
     * @param $from
     * @param array $res
     */
    private function addFirstDate($from, &$res = []): void
    {
        $res[] = [$from['dow'], $from['time'], '23:59'];
    }

    /**
     * @param $from
     * @param $to
     * @param $result
     */
    private function addBetweenDates($from, $to, &$result): void
    {
        $days = $this->getServiceLocator()->get(DatePeriodService::class)->getDow();
        $currentDay = array_search($from['dow'], $days);
        $currentDay = $this->getNextDay($currentDay);
        $lastDay = array_search($to['dow'], $days);
        while ($currentDay != $lastDay) {
            // add this day
            $result[] = [$days[$currentDay], '00:00', '23:59'];
            $currentDay = $this->getNextDay($currentDay);
        }
    }

    /**
     * @param int $day
     * @return int
     */
    private function getNextDay($day = 0): int
    {
        return ++$day > 6 ? 0 : $day;
    }

    /**
     * @param $to
     * @param $result
     */
    private function addLastDate($to, &$result): void
    {
        if (count($result) === 1 && $result[0][0] === $to['dow']) {
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
     * @throws InconsistentDataException
     */
    public function update(DatePeriod $datePeriod): void
    {
        $this->getQuery([self::FIELD_DATE_PERIOD_ID => $datePeriod->id])->delete();
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
        $this->getQuery()->insert($data);
    }
}
