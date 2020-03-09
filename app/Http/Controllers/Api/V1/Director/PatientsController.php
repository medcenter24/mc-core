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

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director;

use Illuminate\Support\Facades\Log;
use Dingo\Api\Http\Response;
use medcenter24\mcCore\App\Http\Controllers\Api\ModelApiController;
use medcenter24\mcCore\App\Http\Requests\Api\PatientRequest;
use medcenter24\mcCore\App\Entity\Patient;
use medcenter24\mcCore\App\Transformers\PatientTransformer;
use League\Fractal\TransformerAbstract;

class PatientsController extends ModelApiController
{
    protected function getDataTransformer(): TransformerAbstract
    {
        return new PatientTransformer();
    }

    protected function getModelClass(): string
    {
        return Patient::class;
    }

    public function index(): Response
    {
        $patients = Patient::orderBy('name')->get();
        return $this->response->collection($patients, new PatientTransformer());
    }

    public function show($id): Response
    {
        $patient = Patient::findOrFail($id);
        return $this->response->item($patient, new PatientTransformer());
    }

    public function store(PatientRequest $request): Response
    {
        $patient = Patient::create($this->getJsonData($request));
        $transformer = new PatientTransformer();
        return $this->response->created(url("/api/director/patients/{$patient->id}"), $transformer->transform($patient));
    }

    public function update($id, PatientRequest $request): Response
    {
        $data = $this->getJsonData($request);
        $patient = Patient::findOrFail($id);
        $patient->name = $data['name'];
        $patient->address = $data['address'];
        $patient->phones = $data['phones'];
        $patient->birthday = $data['birthday'] ?? null;
        $patient->comment = $data['comment'];
        $patient->save();
        Log::info('Patient updated', [$patient, $this->user()]);
        return $this->response->item($patient, new PatientTransformer());
    }

    private function getJsonData(PatientRequest $request): array
    {
        $data = [
            'name' => $request->json('name', ''),
            'address' => $request->json('address', ''),
            'phones' => $request->json('phones', ''),
            'comment' => $request->json('comment', ''),
        ];
        $birthday = $request->json('birthday', false);
        if ($birthday && !empty($birthday)) {
            $data['birthday'] = date('Y-m-d', strtotime($birthday));
        }

        return $data;
    }

    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        Log::info('Patient deleted', [$patient, $this->user()]);
        $patient->delete();
        return $this->response->noContent();
    }
}
