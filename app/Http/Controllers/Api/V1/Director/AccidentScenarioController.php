<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;


use App\Http\Controllers\ApiController;
use App\Services\Scenario\DoctorScenarioService;

class AccidentScenarioController extends ApiController
{
    public function doctorScenario(DoctorScenarioService $doctorScenarioService)
    {
        return $doctorScenarioService->scenario();
    }
}
