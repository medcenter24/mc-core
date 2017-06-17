<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\DoctorSurvey;
use League\Fractal\TransformerAbstract;

class DoctorSurveyTransformer extends TransformerAbstract
{
    public function transform(DoctorSurvey $doctorSurvey)
    {
        return [
            'id' => $doctorSurvey->id,
            'title' => $doctorSurvey->title,
            'description' => $doctorSurvey->description,
            'type' => $doctorSurvey->isDoctor() ? 'doctor' : ''
        ];
    }
}
