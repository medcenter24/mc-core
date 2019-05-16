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

namespace medcenter24\mcCore\App\Models\Scenario;


use medcenter24\mcCore\App\AccidentStatus;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Services\AccidentStatusesService;
use medcenter24\mcCore\App\Services\ScenarioInterface;
use Illuminate\Support\Collection;

/**
 * Class ScenarioModel
 * @package medcenter24\mcCore\App\Models\Scenario
 */
class ScenarioModel implements ScenarioInterface
{

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
