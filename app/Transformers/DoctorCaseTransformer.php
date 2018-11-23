<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\DoctorAccident;
use InvalidArgumentException;
use League\Fractal\TransformerAbstract;

class DoctorCaseTransformer extends TransformerAbstract
{
    public function transform(DoctorAccident $doctorAccident)
    {
        try {
            $visitTime = $doctorAccident->visit_time->setTimezone(auth()->user()->timezone)->format(config('date.systemFormat'));
        } catch (InvalidArgumentException $e) {
            $visitTime = '';
        }
        return [
            'id' => $doctorAccident->id,
            'doctorId' => $doctorAccident->doctor_id,
            'city_id' => $doctorAccident->accident->city_id,
            // api uses only system format if we need to convert it - do it at the frontend
            'createdAt' => $doctorAccident->created_at->setTimezone(auth()->user()->timezone)->format(config('date.systemFormat')),
            'visitTime' => $visitTime,
            'recommendation' => $doctorAccident->recommendation,
            'investigation' => $doctorAccident->investigation,
        ];
    }
}
