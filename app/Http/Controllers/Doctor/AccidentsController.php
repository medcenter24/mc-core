<?php
/**
 * Copyright (c) 2017. 
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Doctor;

use App\DoctorAccident;
use App\Http\Controllers\DoctorController;
use App\Http\Requests\UpdateDoctorAccident;

class AccidentsController extends DoctorController
{

    public function index()
    {
        return DoctorAccident::where('doctor_id', $this->doctor())->get();
    }

    public function show($id)
    {
        return DoctorAccident::where('doctor_id', $this->doctor()->id)->findOrFail($id)->toJson();
    }

    public function update(UpdateDoctorAccident $request, $id)
    {
        /** @var \Illuminate\Database\Eloquent\Model $status */
        $status = DoctorAccident::findOrFail($id);
        foreach ($status->getVisible() as $item) {
            if ($request->has($item)) {
                $status->$item = $request->get($item);
            }
        }
        $status->save();

        return ['success' => true];
    }
}
