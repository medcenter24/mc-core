<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director\Statistics;


use App\Accident;
use App\Http\Controllers\ApiController;
use App\Transformers\statistics\CalendarEventTransformer;
use Illuminate\Http\Request;

class CalendarController extends ApiController
{
    public function index(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');

        $cases = Accident::whereBetween('created_at', [$start.' 00:00:00', $end.' 00:00:00'])->get();
        return $this->response->collection($cases, new CalendarEventTransformer());
    }
}
