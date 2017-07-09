<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\Doctor;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\StoreDoctor;
use App\Http\Requests\Api\UpdateDoctor;
use App\Transformers\CityTransformer;
use App\Transformers\DoctorTransformer;

class DoctorsController extends ApiController
{
    public function index()
    {
        $doctors = Doctor::orderBy('name')->get();
        return $this->response->collection($doctors, new DoctorTransformer());
    }

    public function show($id)
    {
        $doctor = Doctor::findOrFail($id);
        return $this->response->item($doctor, new DoctorTransformer());
    }

    public function store(StoreDoctor $request)
    {
        $doctor = Doctor::create([
            'name' => $request->json('name', ''),
            'description' => $request->json('description', ''),
            'ref_key' => $request->json('ref_key', ''),
        ]);
        $transformer = new DoctorTransformer();
        return $this->response->created(null, $transformer->transform($doctor));
    }

    public function update($id, UpdateDoctor $request)
    {
        $doctor = Doctor::findOrFail($id);
        $doctor->name = $request->json('name', '');
        $doctor->ref_key = $request->json('ref_key', '');
        $doctor->user_id = (int)$request->json('user_id', 0);
        $doctor->description = $request->json('description', '');
        $doctor->save();

        \Log::info('Doctor updated', [$doctor]);

        return $this->response->item($doctor, new DoctorTransformer());
    }

    public function destroy($id)
    {
        $doctor = Doctor::findOrFail($id);
        \Log::info('Doctor deleted', [$doctor]);
        $doctor->delete();
        return $this->response->noContent();
    }

    /**
     * Covered by doctor
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function cities($id)
    {
        $doctor = Doctor::findOrFail($id);
        return $this->response->collection($doctor->cities, new CityTransformer());
    }
}
