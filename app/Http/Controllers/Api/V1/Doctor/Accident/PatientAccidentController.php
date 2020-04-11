<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2020 (original work) MedCenter24.com;
 */

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor\Accident;

use Dingo\Api\Http\Response;
use Illuminate\Http\Request;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Http\Controllers\Api\ApiController;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\AccidentStatusService;
use medcenter24\mcCore\App\Transformers\PatientTransformer;

class PatientAccidentController extends ApiController
{
    use DoctorAccidentControllerTrait;

    /**
     * @return AccidentService
     */
    private function getAccidentService(): AccidentService
    {
        return $this->getServiceLocator()->get(AccidentService::class);
    }

    /**
     * @return AccidentStatusService
     */
    private function getAccidentStatusService(): AccidentStatusService
    {
        return $this->getServiceLocator()->get(AccidentStatusService::class);
    }

    /**
     * @param $id
     * @return Response
     */
    public function patient($id): Response
    {
        /** @var Accident $accident */
        $accident = $this->getAccidentService()->first([AccidentService::FIELD_ID => $id]);
        $this->checkAccess($accident);

        $patient = $accident->patient;
        if (!$patient) {
            $this->response->errorNotFound();
        }

        return $this->response->item($patient, new PatientTransformer());
    }

    /**
     * @param $id
     * @param Request $request
     * @return Response
     * @throws InconsistentDataException
     */
    public function updatePatient($id, Request $request): Response
    {
        /** @var Accident $accident */
        $accident = $this->getAccidentService()->first([AccidentService::FIELD_ID => $id]);
        $this->checkAccess($accident);

        $patient = $accident->patient;
        if (!$patient) {
            $this->response->errorNotFound();
        }

        $changedData = [];

        $newName = $request->get('name','');
        $newComment = $request->get('comment', '');
        $newAddress = $request->get('address', '');
        $newPhone = $request->get('phones', '');
        $newBirthday = $request->get('birthday', '');

        if ($newName !== $patient->name) {
            $changedData['name'] = ['old' => $patient->name, 'new' => $newName];
            $patient->name = $newName;
        }

        if ($newComment !== $patient->comment) {
            $changedData['comment'] = ['old' => $accident->symptoms, 'new' => $newComment];
            $patient->comment = $newComment;
        }

        if ($newAddress !== $patient->address) {
            $changedData['address'] = ['old' => $patient->address, 'new' => $newAddress];
            $patient->address = $newAddress;
        }

        if ($newPhone !== $patient->phones) {
            $changedData['phones'] = ['old' => $patient->phones, 'new' => $newPhone];
            $patient->phones = $newPhone;
        }

        if ($newBirthday !== $patient->birthday) {
            $changedData['birthday'] = ['old' => $patient->birthday, 'new' => $newBirthday];
            $patient->birthday = $newBirthday;
        }

        if (count($changedData)) {
            $status = $this->getAccidentStatusService()->getDoctorAssignedStatus();
            $this->getAccidentService()
                ->setStatus(
                    $accident,
                    $status,
                    'Updated by doctor ' . $this->user()->id . ' ' . json_encode($changedData)
                );
        }
        $patient->save();

        return $this->response->item($patient, new PatientTransformer());
    }
}
