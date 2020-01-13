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

namespace medcenter24\mcCore\App\Transformers;


use medcenter24\mcCore\App\Accident;
use medcenter24\mcCore\App\Helpers\Date;
use medcenter24\mcCore\App\Services\UserService;

/**
 * Used for the output into the data table
 * Class CasesTransformer
 * @package medcenter24\mcCore\App\Transformers
 */
class CaseAccidentTransformer extends AbstractTransformer
{
    /**
     * @param Accident $accident
     * @return array
     */
    public function transform (Accident $accident): array
    {
        $incomePayment = $accident->getAttribute('incomePayment');
        $paymentToCaseable = $accident->getAttribute('paymentToCaseable');
        return [
            'id' => $accident->id, // accident id
            'assistantId' => $accident->assistant_id,
            'patientName' => $accident->patient ? $accident->patient->name : '',
            'repeated' => $accident->parent_id,
            'refNum' => $accident->ref_num ,
            'assistantRefNum' => $accident->assistant_ref_num,
            'caseType' => $accident->caseable_type,
            'createdAt' => Date::sysDate(
                $accident->getAttribute('created_at'),
                $this->getServiceLocator()->get(UserService::class)->getTimezone()
            ),
            'checkpoints' => $accident->checkpoints->implode('title', ', '),
            'status' => $accident->accidentStatus ? $accident->accidentStatus->title : '',
            'city' => $accident->city_id && $accident->city ? $accident->city->title : '',
            'symptoms' => $accident->symptoms,
            'price' => $incomePayment ? $incomePayment->getAttribute('value') : 0,
            'doctorsFee' => $paymentToCaseable ? $paymentToCaseable->getAttribute('value') : 0,
            'handlingTime' => Date::sysDate(
                $accident->getAttribute('handling_at'),
                $this->getServiceLocator()->get(UserService::class)->getTimezone()
            ),
        ];
    }
}
