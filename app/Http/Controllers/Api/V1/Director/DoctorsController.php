<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\Doctor;
use App\Http\Controllers\ApiController;
use App\Transformers\DoctorTransformer;

class DoctorsController extends ApiController
{
    public function index()
    {
        $doctors = Doctor::orderBy('name')->get();
        return $this->response->collection($doctors, new DoctorTransformer());
    }

    public function show($id)
    {
        $doctor = Doctor::findOrFail($id);
        return $this->response->item($doctor, new DoctorTransformer());
    }
}
