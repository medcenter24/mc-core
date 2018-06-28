<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Models\Scenario;


use App\AccidentStatus;
use App\Exceptions\InconsistentDataException;
use App\Services\AccidentStatusesService;
use App\Services\ScenarioInterface;
use Illuminate\Support\Collection;

class ScenarioModel implements ScenarioInterface
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

    public function __construct(AccidentStatusesService $accidentStatusesService, Collection $scenario)
    {
        $this->accidentStatusesService = $accidentStatusesService;
        $this->scenario = $scenario;
    }

    public function scenario()
    {
        return $this->scenario;
    }

    /**
     * @param int $stepId
     * @throws InconsistentDataException
     */
    public function setCurrentStepId($stepId = 0)
    {
        $this->stepId = (int) $this->findStepId($stepId);
    }

    public function current()
    {
        return $this->stepId;
    }

    /**
     * @throws InconsistentDataException
     */
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
                // collection used by models
                $status = $value->accidentStatus;
                if ($status->title == $step->title && $status->type == $step->type) {
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

    /**
     * @param $step
     * @return mixed
     * @throws InconsistentDataException
     */
    public function getStepData($step)
    {
        $stepId = $this->findStepId($step);
        return $this->scenario()->get($stepId);
    }
}
