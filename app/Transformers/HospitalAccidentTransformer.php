<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\Accident;

class HospitalAccidentTransformer extends AccidentTransformer
{
    /**
     * @param Accident $accident
     * @return array
     */
    public function transform (Accident $accident)
    {
        $transformedAccident = parent::transform($accident);

        $hospitalAccident = [
            'status' => $accident->caseable->status,
            'accidentStatusId' => $accident->caseable->accident_status_id,
            'createdAt' => $accident->caseable->created_at->format(config('date.systemFormat')),
            'hospitalId' => $accident->caseable->hospital_id,
        ];

        return array_merge($transformedAccident, $hospitalAccident);
    }
}
