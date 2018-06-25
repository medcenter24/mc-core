<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\Accident;

class DoctorAccidentTransformer extends AccidentTransformer
{
    /**
     * @param Accident $accident
     * @return array
     */
    public function transform (Accident $accident)
    {
        $transformedAccident = parent::transform($accident);

        $doctorAccident = [
            'diagnose' => $accident->caseable->recommendation,
            'investigation' => $accident->caseable->investigation,
            'visitTime' => $accident->caseable->visit_time->format(config('date.systemFormat')),
            'createdAt' => $accident->caseable->created_at->format(config('date.systemFormat')),
            'cityId' => $accident->caseable->city_id,
            'doctorId' => $accident->caseable->doctor_id,
        ];

        return array_merge($transformedAccident, $doctorAccident);
    }
}
