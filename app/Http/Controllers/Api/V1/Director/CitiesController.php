<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\City;
use App\Http\Controllers\ApiController;
use App\Transformers\CityTransformer;

class CitiesController extends ApiController
{
    public function index()
    {
        $cities = City::orderBy('title')->get();
        return $this->response->collection($cities, new CityTransformer());
    }
}
