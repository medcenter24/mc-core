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

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Services\Scenario;

use medcenter24\mcCore\App\Entity\Scenario;
use medcenter24\mcCore\App\Services\ScenarioInterface;
use Illuminate\Support\Collection;

/**
 * Concatenate History with scenario to get the current accident story
 * Class StoryService
 * @package medcenter24\mcCore\App\Services\Scenario
 */
class StoryService implements ScenarioInterface
{
    public const OPTION_STATUS = 'status';

    public const STATUS_VISITED = 'visited';
    public const STATUS_CURRENT = 'current';

    /**
     * @var Collection of the AccidentStatusHistory
     */
    private Collection $history;
    /**
     * @var ScenarioInterface
     */
    private ScenarioInterface $scenario;

    /**
     * Aggregated story
     * @var Collection
     */
    private ?Collection $story = null;

    /**
     * Initialize story
     * @param Collection $history
     * @param ScenarioInterface $scenario
     * @return $this
     */
    public function init(Collection $history, ScenarioInterface $scenario): self
    {
        $this->history = $history;
        $this->scenario = $scenario;

        return $this;
    }

    /**
     * Story by the current history
     */
    public function scenario(): ScenarioInterface
    {
        return $this->scenario;
    }

    public function getStory(): Collection
    {
        if (!$this->story) {
            $this->story = $this->generateStory();
        }

        return $this->story;
    }

    /**
     * Fill passed scenarios steps
     * @return Collection
     */
    private function generateStory(): Collection
    {
        $story = $this->history;
        $scenario = $this->scenario()->scenario()->sortByDesc('order');
        
        // current step is a last action of the history
        $lastAction = $this->history->last();
        if ($lastAction) {
            $currentAccidentStatusId = $lastAction->accident_status_id;
        } else {
            // for the faked accidents is possible that story was not filled
            $currentAccidentStatusId = 0;
        }

        // fill scenario with passed history events
        $scenario->map(static function ($step) use ($currentAccidentStatusId, $story) {
            // step was found in the history
            if ( ($story->search(static function ($item) use ($step) {
                return $item->accident_status_id === $step->accident_status_id;
            })) !== false) {
                if ($step->accident_status_id === $currentAccidentStatusId) {
                    $step->status = self::STATUS_CURRENT;
                } else {
                    $step->status = self::STATUS_VISITED;
                }
            }
        });
        $scenario = $scenario->sortBy('order');
        $scenario = $this->skip($scenario);

        return $scenario;
    }

    /**
     * @param Collection $scenario
     * @return Collection
     */
    private function skip(Collection $scenario): Collection
    {
        // searching for the skipping steps in the scenario
        $skipSteps = $scenario->filter(function ($step) {
            return mb_strpos($step->mode, 'skip:') !== false;
        });

        // delete this steps from the scenario
        $scenario = $scenario->filter(function ($step) {
            return mb_strpos($step->mode, 'skip:') === false;
        });

        $story = $this->history;
        $skipSteps->each(static function(Scenario $skipStep) use ($story, &$scenario) {
            // search in history if this status was assigned
            $foundInHistory = $story->filter(function ($step) use ($skipStep) {
                return $step->accident_status_id === $skipStep->accident_status_id;
            });

            if ($foundInHistory->count()) {
                $skipType = $skipStep->getStatusType();
                $previousMatched = false;
                $newScenario = collect([]);
                $scenario->map(static function ($step) use ($skipStep, $skipType, &$previousMatched, &$newScenario) {
                    if ($step->accidentStatus->type === $skipType) {
                        $previousMatched = true;
                        if ($step->status) {
                            $newScenario->push($step);
                        }
                    } else {
                        if ($previousMatched === true) {
                            $previousMatched = false;
                            $newScenario->push($skipStep);
                        }
                        $newScenario->push($step);
                    }
                });
                $scenario = $newScenario;
            }
        });

        return $scenario;
    }

    public function findStepId($step): int
    {
        return $this->scenario->findStepId($step);
    }

    public function getStepData($step)
    {
        return $this->scenario->getStepData($step);
    }
}
