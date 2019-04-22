<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director\Statistics;


use medcenter24\mcCore\App\Http\Controllers\ApiController;
use medcenter24\mcCore\App\Transformers\statistics\TrafficTransformer;
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
                $join->where('accidents.caseable_type', '=', ' medcenter24\mcCore\App;DoctorAccident')
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
