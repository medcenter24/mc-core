<?php
/**
 * Copyright (c) 2017. 
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Doctor;

use App\DoctorAccident;
use App\Http\Controllers\DoctorController;

class AccidentsController extends DoctorController
{

    public function index()
    {
        return DoctorAccident::where('doctor_id', $this->doctor())->all();
    }

    public function show($id)
    {
        return DoctorAccident::findOrFail($id)->toJson();
    }

    public function update(UpdateDoctorAccident $request, $id)
    {
        /** @var \Eloquent $status */
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
