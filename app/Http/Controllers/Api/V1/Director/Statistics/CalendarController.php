<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director\Statistics;


use App\Accident;
use App\Http\Controllers\ApiController;
use App\Transformers\CalendarEventTransformer;

class CalendarController extends ApiController
{
    public function index()
    {
        $cases = Accident::all();
        return $this->response->collection($cases, new CalendarEventTransformer());
    }
}
