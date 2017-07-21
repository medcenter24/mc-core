<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\Scenario;


use App\AccidentStatus;
use App\AccidentStatusHistory;
use App\Services\ScenarioInterface;
use Illuminate\Support\Collection;

/**
 * Concatenate History with scenario to get the current accident story
 * Class StoryService
 * @package App\Services\Scenario
 */
class StoryService implements ScenarioInterface
{
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
        // last from the history will be current for the scenario
        $this->setCurrentStepId($this->history->last()->accidentStatus);
        return $this;
    }

    public function setCurrentStepId($step = 0)
    {
        $this->scenario->setCurrentStepId($step);
    }

    public function current()
    {
        $this->scenario->current();
    }

    public function next()
    {
        $this->scenario->next();
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
     * @return Collection
     */
    private function generateStory()
    {
        $typeBlocked = false;
        $story = new Collection();

        foreach ($this->scenario()->scenario() as $step) {
            $status = '';

            if ($this->history->search(function ($_item) use($step) {
                return $_item->accident_type_id == $step['accident_status_id'];
            })) {
                $status = 'visited';
            }

            if (mb_strpos($step['mode'], ':') !== false) {
                list($op, $type) = explode(':', $step['mode']);
                dd($op, $type);
            }

            $story->push(['status' => $status], is_array($step) ? $step : $step->toArray());
        }

        return $story;
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
