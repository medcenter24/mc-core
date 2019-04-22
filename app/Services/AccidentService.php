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

namespace medcenter24\mcCore\App\Services;


use medcenter24\mcCore\App\Accident;
use medcenter24\mcCore\App\City;
use medcenter24\mcCore\App\DoctorService;
use medcenter24\mcCore\App\Helpers\Arr;
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
    public function getAccidentServices(Accident $accident): Collection
    {
        $accidentServices = $accident->services;
        if ($accident->caseable) {
            $accidentServices = $accidentServices->merge($accident->caseable->services);
        }
        return $accidentServices ?: collect([]);
    }

    /**
     * Checking accident data and making it correct to write to the DB
     * @param array $accidentData
     * @return array
     */
    public function getFormattedAccidentData(array $accidentData = []): array
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
