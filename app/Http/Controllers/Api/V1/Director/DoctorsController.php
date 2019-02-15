<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\City;
use App\Doctor;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\StoreDoctor;
use App\Http\Requests\Api\UpdateDoctor;
use App\Transformers\CityTransformer;
use App\Transformers\DoctorTransformer;
use Illuminate\Http\Request;

class DoctorsController extends ApiController
{

    protected function getDataTransformer()
    {
        return new DoctorTransformer();
    }

    protected function getModelClass()
    {
        return Doctor::class;
    }

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
            'ref_key' => $request->json('refKey', ''),
            'medical_board_num' => $request->json('medicalBoardNumber', ''),
        ]);
        $transformer = new DoctorTransformer();
        return $this->response->created(null, $transformer->transform($doctor));
    }

    public function update($id, UpdateDoctor $request)
    {
        $doctor = Doctor::findOrFail($id);
        $doctor->name = $request->json('name', '');
        $doctor->ref_key = $request->json('refKey', '');
        $doctor->user_id = (int)$request->json('userId', 0);
        $doctor->description = $request->json('description', '');
        $doctor->medical_board_num = $request->json('medicalBoardNumber', '');
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

    public function setCities($id, Request $request)
    {
        $doctor = Doctor::findOrFail($id);
        $doctor->cities()->detach();
        $cities = $request->json('cities', []);
        if (count($cities)) {
            $doctor->cities()->attach($cities);
        }
        return $this->response->accepted();
    }

    public function getDoctorsByCity($cityId)
    {
        $city = City::findOrFail($cityId);
        return $this->response->collection($city->doctors, new DoctorTransformer());
    }
}
