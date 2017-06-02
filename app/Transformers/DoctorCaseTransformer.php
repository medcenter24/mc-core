<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\Accident;
use App\DoctorAccident;
use League\Fractal\TransformerAbstract;

class DoctorCaseTransformer extends TransformerAbstract
{
    public function transform(DoctorAccident $doctorAccident)
    {
        return [
            'id' => $doctorAccident->id,
            'accident_id' => $doctorAccident->accident->id,
            'accident_status_id' => $doctorAccident->accident_status_id,
            'doctor_id' => $doctorAccident->doctor_id,
            'city_id' => $doctorAccident->city_id,
            'status' => $doctorAccident->status,
            // 'diagnose' => implode("\n", $doctorAccident->diagnostics()),
            'diagnose' => 'something fake',
            'created_at' => $doctorAccident->created_at->format(config('date.actionFormat')),
            'visit_time' => $doctorAccident->visit_time->format(config('date.actionFormat')),
            'recommendation' => $doctorAccident->recommendation,
            'investigation' => $doctorAccident->investigation,
        ];
    }
}
