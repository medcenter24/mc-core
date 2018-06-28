<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\Accident;
use League\Fractal\TransformerAbstract;

/**
 * Used for the output into the data table
 * Class CasesTransformer
 * @package App\Transformers
 */
class CaseAccidentTransformer extends TransformerAbstract
{
    /**
     * @param Accident $accident
     * @return array
     */
    public function transform (Accident $accident)
    {
        return [
            'id' => $accident->id, // accident id
            'assistantId' => $accident->assistant_id,
            'patientName' => $accident->patient ? $accident->patient->name : '',
            'repeated' => $accident->parent_id,
            'refNum' => $accident->ref_num ,
            'assistantRefNum' => $accident->assistant_ref_num,
            'caseType' => $accident->caseable_type,
            'createdAt' => $accident->created_at->setTimezone(auth()->user()->timezone)->format(config('date.systemFormat')), // formatting should be provided by the gui part ->format(config('date.actionFormat')),
            'checkpoints' => $accident->checkpoints->implode('title', ', '),
            'status' => $accident->accidentStatus ? $accident->accidentStatus->title : '',
            'city' => $accident->city_id && $accident->city ? $accident->city->title : '',
            'symptoms' => $accident->symptoms,
            'price' => $accident->income,
            'fee' => $accident->caseable_cost,
        ];
    }
}
