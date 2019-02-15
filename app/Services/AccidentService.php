<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services;


use App\Accident;
use App\City;
use App\DoctorService;
use App\Helpers\Arr;
use Illuminate\Support\Collection;

class AccidentService
{
    /**
     * Calculate Accidents by the referral number
     * @param string $ref
     * @return mixed
     */
    public function getCountByReferralNum ($ref = '')
    {
        return Accident::where('ref_num', $ref)->count();
    }

    /**
     * Get all accidents, assigned to the assistance
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
        return $accident->city_id ?: new City();
    }

    /**
     * @param Accident $accident
     * @return Collection
     */
    public function getAccidentServices(Accident $accident)
    {
        $accidentServices = $accident->services;
        if ($accident->caseable) {
            $accidentServices = $accidentServices->merge($accident->caseable->services);
        }

        return $accidentServices;
    }

    /**
     * Checking accident data and making it correct to write to the DB
     * @param array $accidentData
     * @return array
     */
    public function getFormattedAccidentData(array $accidentData = [])
    {
        foreach ([
            'handling_time' => null,
            'assistant_ref_num' => '',
            'contacts' => '',
            'symptoms' => '',
            'created_by' => 0,
            'parent_id' => 0,
            'patient_id' => 0,
            'accident_type_id' => 0,
            'accident_status_id' => 0,
            'assistant_id' => 0,
            'city_id' => 0,
            'ref_num' => '',
            'title' => '',
            'address' => '',
            'form_report_id' => 0,
            'caseable_payment_id' => 0,
            'income_payment_id' => 0,
            'assistant_payment_id' => 0,
        ] as $key => $val) {
            Arr::setDefault($accidentData, $key, $val);
        }

        return $accidentData;
    }

    /**
     * Check if the accident was closed
     * @param Accident $accident
     * @return bool
     */
    public function isClosed(Accident $accident)
    {
        return $accident->accidentStatus
            && $accident->accidentStatus->title == AccidentStatusesService::STATUS_CLOSED
            && $accident->accidentStatus->type == AccidentStatusesService::TYPE_ACCIDENT;
    }
}
