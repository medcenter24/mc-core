<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\Scenario;


use App\Scenario;
use App\Services\ScenarioInterface;
use Illuminate\Support\Collection;

/**
 * Concatenate History with scenario to get the current accident story
 * Class StoryService
 * @package App\Services\Scenario
 */
class StoryService implements ScenarioInterface
{
    const OPTION_STATUS = 'status';

    const STATUS_VISITED = 'visited';
    const STATUS_CURRENT = 'current';

    /**
     * @var Collection of the AccidentStatusHistory
     */
    private $history;
    /**
     * @var ScenarioInterface
     */
    private $scenario;

    /**
     * Aggregated story
     * @var array
     */
    private $story;

    /**
     * Initialize story
     * @param Collection $history
     * @param ScenarioInterface $scenario
     * @return $this
     */
    public function init(Collection $history, ScenarioInterface $scenario)
    {
        $this->history = $history;
        $this->scenario = $scenario;

        return $this;
    }

    /**
     * Story by the current history
     */
    public function scenario()
    {
        return $this->scenario;
    }

    public function getStory()
    {
        if (!$this->story) {
            $this->story = $this->generateStory();
        }

        return $this->story;
    }

    /**
     * Fill passed scenarios steps
     */
    private function generateStory()
    {
        $story = $this->history;
        $scenario = $this->scenario()->scenario()->sortByDesc('order');
        // latest step will be marked as current
        $isCurrent = true;
        $scenario->map(function ($step) use ($story, &$isCurrent) {
            // step was found in the history
            if ( ($foundId = $story->search(function ($item) use ($step) {
                return $item->accident_status_id == $step->accident_status_id;
            })) !== false) {
                if ($isCurrent) {
                    $isCurrent = false; // only one current per story
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
    private function skip(Collection $scenario)
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
        $skipSteps->each(function(Scenario $skipStep) use ($story, &$scenario) {
            // search in history if this status was assigned
            $foundInHistory = $story->filter(function ($step) use ($skipStep) {
                return $step->accident_status_id == $skipStep->accident_status_id;
            });

            if ($foundInHistory->count()) {
                $skipType = $skipStep->getStatusType();
                $previousMatched = false;
                $newScenario = collect([]);
                $scenario->map(function ($step) use ($skipStep, $skipType, &$previousMatched, &$newScenario) {
                    if ($step->accidentStatus->type == $skipType) {
                        $previousMatched = true;
                        if ($step->status) {
                            $newScenario->push($step);
                        }
                    } else {
                        if ($previousMatched == true) {
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

    public function findStepId($step)
    {
        return $this->scenario->findStepId($step);
    }

    public function getStepData($step)
    {
        return $this->scenario->getStepData($step);
    }
}
