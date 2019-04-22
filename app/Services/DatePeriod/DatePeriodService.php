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

namespace medcenter24\mcCore\App\Services\DatePeriod;


use medcenter24\mcCore\App\DatePeriod;
use medcenter24\mcCore\App\Events\DatePeriodChangedEvent;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use Illuminate\Support\Facades\Log;

class DatePeriodService
{
    public CONST TIME = 'time';
    public CONST DOW = 'dow'; // day of week

    protected static $dow = [
        0 => 'sun',
        1 => 'mon',
        2 => 'tues',
        3 => 'wed',
        4 => 'thurs',
        5 => 'fri',
        6 => 'sat',
    ];

    /**
     * Checks that string is a period
     * @param string $val
     * @return bool
     */
    public function isPeriod(string $val = ''): bool
    {
        $isPeriod = false;
        try {
            $period = $this->parsePeriod($val);
            $isPeriod = isset($period[self::TIME]) && $period[self::TIME];
        } catch (InconsistentDataException $e) {
        }
        return $isPeriod;
    }

    /**
     * @param string $val
     * @return array
     * @throws InconsistentDataException
     */
    public function parsePeriod(string $val = ''): array
    {
        $res = [
            self::TIME => '',
            self::DOW => '',
        ];
        $val = trim($val);
        $parts = explode(' ', $val);

        switch (count($parts)) {
            case 1:
                if ($this->isTime($parts[0])) {
                    $res[self::TIME] = $parts[0];
                    break;
                }
                break;
            case 2:
                if ($this->isDow($parts[0]) && $this->isTime($parts[1])) {
                    $res[self::DOW] = $parts[0];
                    $res[self::TIME] = $parts[1];
                    break;
                }
                break;
            default:
                throw new InconsistentDataException('Incorrect period format');
        }
        return $res;
    }

    /**
     * Returns list of day of week
     */
    public function getDow(): array
    {
        return self::$dow;
    }

    /**
     * Value is day of week
     * @param string $val
     * @return bool
     */
    protected function isDow (string $val = ''): bool
    {
        return in_array($val, self::$dow, false);
    }

    /**
     * Value is a time
     * @param string $val
     * @return bool
     */
    protected function isTime (string $val = ''): bool
    {
        $isTime = false;

        $time = explode(':', $val);
        if (count($time) === 2) {
            $hr = trim(array_shift($time));
            $min = trim(array_shift($time));

            if (!preg_match('/\D+/', $hr) && !preg_match('/\D+/', $min)) {
                if ($hr < 24 && $min < 60) {
                    $isTime = true;
                }
            }
        }

        return $isTime;
    }

    /**
     * Save data to storage
     * @param array $data
     * @return mixed
     */
    public function save(array $data = [])
    {
        if (!array_key_exists('day_of_week', $data)) {
            $data['day_of_week'] = 0;
        }
        
        if (array_key_exists('id', $data) && $data['id']) {
            $datePeriod = DatePeriod::findOrFail($data['id']);
            $datePeriod->update($data);
            Log::info('Period updated', [$datePeriod, auth()->user()]);
        } else {
            $datePeriod = DatePeriod::create($data);
            Log::info('Period created', [$datePeriod, auth()->user()]);
        }

        event(new DatePeriodChangedEvent($datePeriod));

        return $datePeriod;
    }
}
