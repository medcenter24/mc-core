<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services;
use App\AccidentStatus;

/**
 * Scenario is a set of the statuses which should be done for the case completion
 * Each case should be presented as a hospital case or doctor case scenarios
 *
 * Interface ScenarioInterface
 * @package App\Services
 */
interface ScenarioInterface
{
    /**
     * Current position in the scenario
     * @return int
     */
    public function current();

    /**
     * Next step Id
     * @return int
     */
    public function next();

    /**
     * Current scenario
     * @return \Illuminate\Support\Collection
     */
    public function scenario();

    /**
     * @param int
     */
    public function setCurrentStepId($step = 0);

    /**
     * @param int|array|AccidentStatus $step
     * @return int stepId
     */
    public function findStepId($step);

    /**
     * @param int|array|AccidentStatus $step
     * @return mixed
     */
    public function getStepData($step);
}
