<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\Assistant;
use App\City;
use App\Doctor;
use App\DoctorService;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\FinanceRequest;
use App\Services\CaseServices\CaseFinanceService;

class FinanceController extends ApiController
{
    /**
     * List of rules
     */
    public function index() {}

    public function show($id) {}

    /**
     * Add new rule
     * @param FinanceRequest $request
     * @param CaseFinanceService $caseFinanceService
     * @return \Dingo\Api\Http\Response
     */
    public function store(FinanceRequest $request, CaseFinanceService $caseFinanceService) {
        $caseFinanceCondition = $caseFinanceService->factory();
        $doctor = $request->json('doctor', false);
        if ($doctor && isset($doctor['id'])) {
            $caseFinanceCondition->if(Doctor::class, $doctor['id']);
        }
        $assistant = $request->json('assistant', false);
        if ($assistant && isset($assistant['id'])) {
            $caseFinanceCondition->if(Assistant::class, $assistant['id']);
        }
        $city = $request->json('city', false);
        if ($city && isset($city['id'])) {
            $caseFinanceCondition->if(City::class, $city['id']);
        }

        // condition base on the full match
        // if you need to have only one service in condition or condition for each of the provided service:
        // you need to create new conditions from the gui one by one
        $services = $request->json('services', false);
        if ($services && count($services)) {
            foreach ($services as $service) {
                $caseFinanceCondition->if(DoctorService::class, $service['id']);
            }
        }

        $caseFinanceCondition->thenPrice($request->json('priceAmount', 0));
        $caseFinanceService->saveCondition($caseFinanceCondition);

        return $this->response->created();
    }

    /**
     * Update existing rule
     * @param $id
     * @param FinanceRequest $request
     */
    public function update($id, FinanceRequest $request) {}

    /**
     * Destroy rule
     */
    public function destroy($id) {}
}
