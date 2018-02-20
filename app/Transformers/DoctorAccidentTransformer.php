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
            'status' => $accident->caseable->status,
            'accident_status_id' => $accident->caseable->accident_status_id,
            'visit_time' => $accident->caseable->visit_time->format(config('date.systemFormat')),
            'created_at' => $accident->caseable->created_at->format(config('date.systemFormat')),
            'city_id' => $accident->caseable->city_id,
            'doctor_id' => $accident->caseable->doctor_id,
        ];

        return array_merge($transformedAccident, $doctorAccident);
    }
}
