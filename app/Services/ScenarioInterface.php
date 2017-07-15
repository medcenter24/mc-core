<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services;

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
     * Next step
     * @return int
     */
    public function next();

    /**
     * Current scenario
     * @return array
     */
    public function scenario();
}
