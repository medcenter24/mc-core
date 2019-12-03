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
use League\Fractal\TransformerAbstract;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;
use medcenter24\mcCore\App\Services\UserService;

/**
 * Used for the output into the data table
 * Class CasesTransformer
 * @package medcenter24\mcCore\App\Transformers
 */
class CaseAccidentTransformer extends TransformerAbstract
{
    use ServiceLocatorTrait;

    /**
     * @param Accident $accident
     * @return array
     */
    public function transform (Accident $accident): array
    {
        return [
            'id' => $accident->id, // accident id
            'assistantId' => $accident->assistant_id,
            'patientName' => $accident->patient ? $accident->patient->name : '',
            'repeated' => $accident->parent_id,
            'refNum' => $accident->ref_num ,
            'assistantRefNum' => $accident->assistant_ref_num,
            'caseType' => $accident->caseable_type,
            'createdAt' => $accident->created_at
                ->setTimezone($this->getServiceLocator()
                    ->get(UserService::class)->getTimezone())
                ->format(config('date.systemFormat')), // formatting should be provided by the gui part ->format(config('date.actionFormat')),
            'checkpoints' => $accident->checkpoints->implode('title', ', '),
            'status' => $accident->accidentStatus ? $accident->accidentStatus->title : '',
            'city' => $accident->city_id && $accident->city ? $accident->city->title : '',
            'symptoms' => $accident->symptoms,
            'price' => $accident->getAttribute('incomePayment')->getAttribute('value'),
            'doctorsFee' => $accident->getAttribute('paymentToCaseable')->getAttribute('value'),
        ];
    }
}
