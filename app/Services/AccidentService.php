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
    public function getCasesQuery(array $filters = [])
    {
        return Accident::orderBy('created_at', 'desc');
    }

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
