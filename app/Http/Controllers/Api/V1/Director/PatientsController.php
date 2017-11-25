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
    public function index()
    {
        $patient = Patient::orderBy('name')->get();
        return $this->response->collection($patient, new PatientTransformer());
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
        return $this->response->created(null, $transformer->transform($patient));
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
        $this->response->item($patient, new PatientTransformer());
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
