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

namespace App\Http\Controllers\Api\V1\Director;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\PatientRequest;
use App\Patient;
use App\Transformers\PatientTransformer;
use League\Fractal\TransformerAbstract;

class PatientsController extends ApiController
{
    protected function getDataTransformer(): TransformerAbstract
    {
        return new PatientTransformer();
    }

    protected function getModelClass(): string
    {
        return Patient::class;
    }

    public function index()
    {
        $patients = Patient::orderBy('name')->get();
        return $this->response->collection($patients, new PatientTransformer());
    }

    public function show($id)
    {
        return $this->response->item(
            Patient::findOrFail($id),
            new PatientTransformer()
        );
    }

    public function store(PatientRequest $request)
    {
        $patient = Patient::create($this->getJsonData($request));
        $transformer = new PatientTransformer();
        return $this->response->created(url("/api/director/patients/{$patient->id}"), $transformer->transform($patient));
    }

    public function update($id, PatientRequest $request)
    {
        $data = $this->getJsonData($request);
        $patient = Patient::findOrFail($id);
        $patient->name = $data['name'];
        $patient->address = $data['address'];
        $patient->phones = $data['phones'];
        $patient->birthday = isset($data['birthday']) ? $data['birthday'] : null;
        $patient->comment = $data['comment'];
        $patient->save();
        \Log::info('Patient updated', [$patient, $this->user()]);
        return $this->response->item($patient, new PatientTransformer());
    }

    private function getJsonData(PatientRequest $request) {
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
        \Log::info('Patient deleted', [$patient, $this->user()]);
        $patient->delete();
        return $this->response->noContent();
    }
}
