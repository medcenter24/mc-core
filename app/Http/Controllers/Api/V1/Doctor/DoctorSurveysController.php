<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Doctor;

use App\DoctorSurvey;
use App\Http\Controllers\ApiController;
use App\Transformers\DoctorSurveyTransformer;

class DoctorSurveysController extends ApiController
{
    public function index()
    {
        $surveys = DoctorSurvey::orderBy('title')->get();
        return $this->response->collection($surveys, new DoctorSurveyTransformer());
    }
}
