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
            'assistant_id' => $accident->assistant_id,
            'repeated' => $accident->parent_id,
            'ref_num' => $accident->ref_num,
            'case_type' => $accident->caseable_type,
            'created_at' => $accident->created_at->format(config('date.actionFormat')),
            'checkpoints' => $accident->checkpoints->count(),
            'status' => 'new',
            'city' => $accident->city_id ? $accident->city->title : '',
            'symptoms' => $accident->symptoms,
        ];
    }
}
