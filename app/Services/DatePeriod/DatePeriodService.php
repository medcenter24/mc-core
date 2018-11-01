<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Services\DatePeriod;


use App\DatePeriod;
use App\Events\DatePeriodChangedEvent;
use App\Exceptions\InconsistentDataException;
use Auth;

class DatePeriodService
{
    CONST TIME = 'time';
    CONST DOW = 'dow'; // day of week

    protected $dow = [
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
    public function isPeriod(string $val = '')
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
    public function parsePeriod(string $val = '')
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
                    $res[self::TIME] = $parts;
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
    public function getDow()
    {
        return $this->dow;
    }

    /**
     * Value is day of week
     * @param string $val
     * @return bool
     */
    protected function isDow (string $val = '')
    {
        return in_array($val, $this->dow);
    }

    /**
     * Value is a time
     * @param string $val
     * @return bool
     */
    protected function isTime (string $val = '')
    {
        $isTime = false;

        $time = explode(':', $val);
        if (count($time) == 2) {
            $hr = trim(array_shift($time));
            $min = trim(array_shift($time));

            if (!preg_match('/[^0-9]+/', $hr) && !preg_match('/[^0-9]+/', $min)) {
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
        if (array_key_exists('id', $data) && $data['id']) {
            $datePeriod = DatePeriod::findOrFail($data['id']);
            $datePeriod->update($data);
            \Log::info('Period updated', [$datePeriod, Auth::user()]);
        } else {
            $datePeriod = DatePeriod::create($data);
            \Log::info('Period created', [$datePeriod, Auth::user()]);
        }

        event(new DatePeriodChangedEvent($datePeriod));

        return $datePeriod;
    }
}
