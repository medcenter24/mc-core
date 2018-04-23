<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\PatientRequest;
use App\Patient;
use App\Transformers\PatientTransformer;

class PatientsController extends ApiController
{
    protected function getDataTransformer()
    {
        return new PatientTransformer();
    }

    protected function getModelClass()
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
