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
            'doctor_id' => $doctorAccident->doctor_id,
            'city_id' => $doctorAccident->city_id,
            // api uses only system format if we need to convert it - do it at the frontend
            'created_at' => $doctorAccident->created_at->setTimezone(auth()->user()->timezone)->format(config('date.systemFormat')),
            'visit_time' => $doctorAccident->visit_time ? $doctorAccident->visit_time->setTimezone(auth()->user()->timezone)->format(config('date.systemFormat')) : '',
            'recommendation' => $doctorAccident->recommendation,
            'investigation' => $doctorAccident->investigation,
        ];
    }
}
