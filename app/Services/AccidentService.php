<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services;


use App\Accident;
use App\City;

class AccidentService
{
    public function getCountByReferralNum ($ref = '')
    {
        return Accident::where('ref_num', $ref)->count();
    }

    /**
     * @param $assistanceId
     * @param $fromDate
     * @return int
     */
    public function getCountByAssistance($assistanceId, $fromDate)
    {
        return Accident::where('created_at', '>=', $fromDate)
            ->where('assistant_id', '=', $assistanceId)
            ->count();
    }

    /**
     * @param array $filters
     * @return mixed
     */
    public function getCasesQuery(array $filters = [])
    {
        return Accident::orderBy('created_at', 'desc');
    }

    /**
     * @param Accident $accident
     * @return City|mixed
     */
    public function getCity(Accident $accident)
    {
        $city = new City();
        \Log::debug('asdf', ['f' => $accident->id, 'd' => $accident->caseable->city]);
        if ($accident->caseable->city_id) {
            $city = $accident->caseable->city;
        } elseif ($accident->city_id) {
            $city = $accident->city;
        }

        return $city;
    }
}
