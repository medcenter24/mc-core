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

namespace medcenter24\mcCore\App\Services;
use Illuminate\Support\Collection;
use medcenter24\mcCore\App\Entity\AccidentStatus;

/**
 * Scenario is a set of the statuses which should be done for the case completion
 * Each case should be presented as a hospital case or doctor case scenarios
 *
 * Interface ScenarioInterface
 * @package medcenter24\mcCore\App\Services
 */
interface ScenarioInterface
{
    /**
     * Current position in the scenario
     * @return int
     */
    // public function current();

    /**
     * Next step Id
     * @return int
     */
    // public function next();

    /**
     * Current scenario
     * @return Collection
     */
    public function scenario();

    /**
     * @param int
     */
    //public function setCurrentStepId($step = 0);

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
