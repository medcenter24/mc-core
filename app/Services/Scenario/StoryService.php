<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\Scenario;


use App\AccidentStatusHistory;
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
     * @var int
     */
    private $currentStepId = 0;

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

        // last from the history will be current for the scenario
        if ($this->history->count()) {
            $this->setCurrentStepId($this->history->last()->accidentStatus);
        }
        return $this;
    }

    public function setCurrentStepId($step = 0)
    {
        $this->scenario->setCurrentStepId($step);
        $this->currentStepId = $this->scenario->current();
    }

    public function current()
    {
        return $this->scenario->current();
    }

    public function next()
    {
        return $this->scenario->next();
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
     * Create story from the history and accepted scenario
     *         // merging history into the scenario with needed statuses
     * @return Collection
     */
    private function generateStory()
    {
        $skipper = false;
        $story = $this->scenario()->scenario();

        // mark all steps which were visited by the student as visited
        /** @var AccidentStatusHistory $passed */
        foreach ($this->history as $passed) {
            $id = $story->search(function ($item) use ($passed) {
                return $passed->accident_status_id == $item['accident_status_id'];
            });
            if ($id !== false) {
                // init skipper if needed
                $step = $story->get($id);
                $step->status = $this->current() == $id ? self::STATUS_CURRENT : self::STATUS_VISITED;

                // on the conditional scenario step and user has made this step
                if (mb_strpos($step['mode'], ':') !== false
                    && $step['status'] == self::STATUS_VISITED) {
                        list($op, $type) = explode(':', $step['mode']);
                        $skipper = [
                            'operation' => $op,
                            'type' => $type,
                        ];
                    }
                }
            }

        foreach ($story as $key => $step) {
            if (
                // skip steps by condition from the $skipper
                // or skip condition which has not been reached
                ( ($this->skipped($skipper, $step) || mb_strpos($step['mode'], ':') !== false)
                    && (!isset($step[self::OPTION_STATUS]) || $step[self::OPTION_STATUS] != self::STATUS_VISITED))
            ) {
                $story->forget($key);
            }
        }

        return $story;
    }

    private function skipped($skipper, $step)
    {
        $skipped = false;
        if (is_array($skipper)) {
            if (isset($skipper['operation']) && $skipper['operation'] == 'skip') {
                if ($step->status != self::STATUS_VISITED && $step->accidentStatus->type == $skipper['type']) {
                    $skipped = true;
                }
            }
        }
        return $skipped;
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
