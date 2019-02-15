<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\Hospital;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\StoreHospital;
use App\Http\Requests\Api\UpdateHospital;
use App\Transformers\HospitalTransformer;

class HospitalsController extends ApiController
{
    protected function getDataTransformer()
    {
        return new HospitalTransformer();
    }

    protected function getModelClass()
    {
        return Hospital::class;
    }

    public function index()
    {
        $hospitals = Hospital::orderBy('title')->get();
        return $this->response->collection($hospitals, new HospitalTransformer());
    }

    public function show($id)
    {
        $hospital = Hospital::findOrFail($id);
        return $this->response->item($hospital, new HospitalTransformer());
    }

    public function store(StoreHospital $request)
    {
        $hospital = Hospital::create([
            'title' => $request->json('title', ''),
            'description' => $request->json('description', ''),
            'address' => $request->json('address', ''),
            'phones' => $request->json('phones', ''),
            'ref_key' => $request->json('refKey', ''),
        ]);
        $transformer = new HospitalTransformer();
        return $this->response->created(null, $transformer->transform($hospital));
    }

    public function update($id, UpdateHospital $request)
    {
        $hospital = Hospital::findOrFail($id);
        $hospital->title = $request->json('title', '');
        $hospital->ref_key = $request->json('refKey', '');
        $hospital->address = $request->json('address', '');
        $hospital->description = $request->json('description', '');
        $hospital->phones = $request->json('phones', '');
        $hospital->save();

        \Log::info('Hospital updated', [$hospital, $this->user()]);

        return $this->response->item($hospital, new HospitalTransformer());
    }

    public function destroy($id)
    {
        $hospital = Hospital::findOrFail($id);
        \Log::info('Hospital deleted', [$hospital, $this->user()]);
        $hospital->delete();
        return $this->response->noContent();
    }
}
