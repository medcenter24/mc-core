<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Doctor;

use App\DoctorService;
use App\Http\Controllers\ApiController;
use App\Transformers\DoctorServiceTransformer;

class DoctorServicesController extends ApiController
{
    public function index()
    {
        $services = DoctorService::orderBy('title', 'desc')->get();
        return $this->response->collection($services, new DoctorServiceTransformer());
    }
}
