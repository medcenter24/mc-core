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
        $patient = Patient::create([
            'name' => $request->json('name', ''),
            'address' => $request->json('address', ''),
            'phones' => $request->json('phones', ''),
            'birthday' => $request->json('birthday', ''),
            'comment' => $request->json('comment', ''),
        ]);
        $transformer = new PatientTransformer();
        return $this->response->created(null, $transformer->transform($patient));
    }

    public function update($id, PatientRequest $request)
    {
        $patient = Patient::findOrFail($id);
        $patient->name = $request->json('name', '');
        $patient->address = $request->json('address', '');
        $patient->phones = $request->json('phones', '');
        $patient->birthday = $request->json('birthday', '');
        $patient->comment = $request->json('comment', '');
        $patient->save();
        \Log::info('Patient updated', [$patient, $this->user()]);
        $this->response->item($patient, new PatientTransformer());
    }

    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        \Log::info('Patient deleted', [$patient, $this->user()]);
        $patient->delete();
        return $this->response->noContent();
    }
}
