<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\Accident;
use App\Services\Scenario\DoctorScenarioService;
use App\Services\Scenario\StoryService;
use League\Fractal\TransformerAbstract;

/**
 * Used for the output into the data table
 * Class CasesTransformer
 * @package App\Transformers
 */
class CaseAccidentTransformer extends TransformerAbstract
{
    private $storyService;
    private $scenarioService;

    public function __construct()
    {
        $this->storyService = new StoryService();
        $this->scenarioService = new DoctorScenarioService();
    }

    /**
     * @param Accident $accident
     * @return array
     */
    public function transform (Accident $accident)
    {
        return [
            'id' => $accident->id, // accident id
            'assistant_id' => $accident->assistant_id,
            'patient_name' => $accident->patient ? $accident->patient->name : '',
            'repeated' => $accident->parent_id,
            'ref_num' => $accident->ref_num ,
            'assistant_ref_num' => $accident->assistant_ref_num,
            'case_type' => $accident->caseable_type,
            'created_at' => $accident->created_at->format(config('date.systemFormat')), // formatting should be provided by the gui part ->format(config('date.actionFormat')),
            'checkpoints' => $accident->checkpoints->implode('title', ', '),
            'status' => $accident->accidentStatus ? $accident->accidentStatus->title : '',
            'city' => $accident->city_id ? $accident->city->title : '',
            'symptoms' => $accident->symptoms,
        ];
    }
}
