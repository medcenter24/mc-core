<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\Scenario;


use App\AccidentStatus;
use App\DoctorAccident;
use App\Exceptions\InconsistentDataException;
use App\Scenario;
use App\Services\ScenarioInterface;
use Illuminate\Support\Collection;

class DoctorScenarioService implements ScenarioInterface
{
    /**
     * Current step
     * @var int
     */
    private $stepId;

    /**
     * @var Collection
     */
    private $scenario;

    public function setScenario(Collection $scenario)
    {
        $this->scenario = $scenario;
    }

    public function scenario()
    {
        if (!$this->scenario) {
            $this->scenario = Scenario::where('tag', DoctorAccident::class)->orderBy('order')->get();
        }

        return $this->scenario;
    }

    public function setCurrentStepId($stepId = 0)
    {
        $this->stepId = $this->findStepId($stepId);
    }

    public function current()
    {
        return $this->stepId ?: 1;
    }

    public function next()
    {
        $count = count($this->scenario());
        if ($this->current() < $count) {
            $this->setCurrentStepId($this->current()+1);

        }
    }

    /**
     * Looking for the step (by id or using status tag)
     * @param $step
     * @return int|string
     * @throws InconsistentDataException
     */
    public function findStepId($step)
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

            if (!$step) {
                throw new InconsistentDataException('Invalid data for the step selection. Step was not found.');
            }
        } elseif (is_integer($step)) {
            $count = count($this->scenario());
            if ($step > $count || $step <= 0) {
                throw new InconsistentDataException('Invalid data for the step selection. Step is not in range.');
            }
        } elseif ($step instanceof AccidentStatus) {
            return $this->findStepId([
                'title' => $step->title,
                'type' => $step->type,
            ]);
        } else {
            throw new InconsistentDataException('Invalid data for the step selection. Format of the step is not defined.');
        }

        return $step;
    }

    public function getStepData($step)
    {
        $stepId = $this->findStepId($step);
        return $this->scenario()->get($stepId);
    }
}
