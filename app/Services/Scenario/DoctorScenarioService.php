<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\Scenario;


use App\AccidentStatus;
use App\Exceptions\InconsistentDataException;
use App\Services\ScenarioInterface;

class DoctorScenarioService implements ScenarioInterface
{
    /**
     * Current step
     * @var int
     */
    private $step;

    // TODO implement events on the step changed and save everything to the historiable
    //  todo historiable could be used to show scenarios line with dates when it happened
    public function scenario()
    {
        return [
            1 => [
                'title' => 'new',
                'type'  => 'accident',
            ],
            2 => [
                'title' => 'assigned',
                'type'  => 'doctor',
            ],
            3 => [
                'title' => 'in_progress',
                'type'  => 'doctor',
            ],
            4 => [
                'title' => 'sent',
                'type'  => 'doctor',
            ],
            5 => [
                'title' => 'paid',
                'type'  => 'doctor',
            ],
            6 => [
                'title' => 'rejected',
                'type'  => 'doctor',
                'mode'  => 'alternate', // don't used by scenario but doctor could set this step in any time that he want
            ],
            7 => [
                'title' => 'closed',
                'type' => 'accident',
            ],
        ];
    }

    public function set($step)
    {
        $this->step = $this->findStep($step);
    }

    public function current()
    {
        return $this->step;
    }

    public function next()
    {
        if (!isset($this->step)) {
            throw new InconsistentDataException('Scenario is not defined.');
        }
        $count = count($this->scenario());
        if ($this->step < $count) {
            $this->step++;

        }
    }

    /**
     * Looking for the step (by id or using status tag)
     * @param $step
     * @return int|string
     * @throws InconsistentDataException
     */
    private function findStep($step)
    {
        if (is_array($step)) {
            if (!isset($step['title']) || !isset($step['type']) ) {
                throw new InconsistentDataException('Invalid data for the step selection. Step was not found.');
            }

            foreach ($this->scenario() as $key => $value) {
                if ($step['title'] == $value['title'] && $step['type'] == $value['type']) {
                    $step = $key;
                    break;
                }
            }
        } elseif (is_integer($step)) {
            $count = count($this->scenario());
            if ($step > $count || $step <= 0) {
                throw new InconsistentDataException('Invalid data for the step selection. Step is not in range.');
            }
        } elseif ($step instanceof AccidentStatus) {
            return $this->findStep([
                'title' => $step->title,
                'type' => $step->type,
            ]);
        } else {
            throw new InconsistentDataException('Invalid data for the step selection. Format of the step is not defined.');
        }

        return $step;
    }
}
