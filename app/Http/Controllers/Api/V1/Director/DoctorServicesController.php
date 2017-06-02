<?php

namespace App\Http\Controllers\Api\V1\Director;

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
