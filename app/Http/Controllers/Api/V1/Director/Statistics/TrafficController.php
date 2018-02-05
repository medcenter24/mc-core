<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director\Statistics;


use App\Http\Controllers\ApiController;
use App\Transformers\statistics\TrafficTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrafficController extends ApiController
{
    public function doctors(Request $request)
    {
        $year = $request->input('year');
        if (!$year) {
            $year = (new Carbon())->format('Y');
        }

        $statistic = DB::table('accidents')
            ->join('doctor_accidents', function($join){
                $join->where('accidents.caseable_type', '=', 'App\DoctorAccident')
                    ->on('accidents.caseable_id', '=', 'doctor_accidents.id');
            })
            ->join('doctors', 'doctors.id', '=', 'doctor_accidents.doctor_id')
            ->select('doctors.id as id', 'doctors.name as name', DB::raw('count(accidents.id) as cases_count'))
            ->whereBetween('accidents.created_at', [$year.'-01-01 00:00:00', $year.'-12-31 23:59:59'])
            ->groupBy('doctors.id')
            ->orderBy('cases_count', 'desc')
            ->get();

        return $this->response->collection($statistic, new TrafficTransformer());
    }

    public function assistants(Request $request)
    {
        $year = $request->input('year');
        if (!$year) {
            $year = (new Carbon())->format('Y');
        }

        $statistic = DB::table('accidents')
            ->join('assistants', 'assistants.id', '=', 'accidents.assistant_id')
            ->select('assistants.id as id', 'assistants.title as name', DB::raw('count(accidents.id) as cases_count'))
            ->whereBetween('accidents.created_at', [$year.'-01-01 00:00:00', $year.'-12-31 23:59:59'])
            ->groupBy('assistants.id')
            ->orderBy('cases_count', 'desc')
            ->get();

        return $this->response->collection($statistic, new TrafficTransformer());
    }
}
