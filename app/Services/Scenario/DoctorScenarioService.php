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
use App\Services\AccidentStatusesService;
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

    /**
     * @var AccidentStatusesService
     */
    private $accidentStatusesService;

    /**
     * @var ScenarioService
     */
    private $scenarioService;

    public function __construct(AccidentStatusesService $accidentStatusesService, ScenarioService $scenarioService)
    {
        $this->accidentStatusesService = $accidentStatusesService;
        $this->scenarioService = $scenarioService;
    }

    public function setScenario(Collection $scenario)
    {
        $this->scenario = $scenario;
    }

    public function scenario()
    {
        if (!$this->scenario) {
            $this->scenario = $this->scenarioService->getScenarioByTag(DoctorAccident::class);
        }

        return $this->scenario;
    }

    public function setCurrentStepId($stepId = 0)
    {
        $this->stepId = (int) $this->findStepId($stepId);
    }

    public function current()
    {
        return $this->stepId;
    }

    public function next()
    {
        $step = $this->current() + 1;
        if ($this->scenario()->has($step)) {
            $this->setCurrentStepId($step);
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

            $accidentStatus = $this->accidentStatusesService->firstOrFail($step);
            $step = $this->findStepId($accidentStatus);

        } elseif (is_integer($step)) {
            if (!$this->scenario()->has($step)) {
                \Log::error('Step not found', ['step' => $step, 'scenario' => $this->scenario()]);
                throw new InconsistentDataException('Invalid data for the step selection. Step is not in range.');
            }
        } elseif ($step instanceof AccidentStatus) {
            foreach ($this->scenario() as $key => $value) {
                if ($value['title'] == $step->title && $value['type'] == $step->type) {
                    $step = $key;
                    break;
                }
            }
        }

        if (!is_integer($step)) {
            \Log::error('Step can not be found', ['step' => $step, 'scenario' => $this->scenario()]);
            throw new InconsistentDataException('Invalid data for the step selection. Step is not defined.');
        }

        return $step;
    }

    public function getStepData($step)
    {
        $stepId = $this->findStepId($step);
        return $this->scenario()->get($stepId);
    }
}
