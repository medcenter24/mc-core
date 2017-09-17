<?php
/**
 * Copyright (c) 2017. 
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Director;


use App\Patient;
use App\Http\Controllers\DirectorController;
use App\Http\Requests\StorePatient;
use App\Http\Requests\UpdatePatient;

class PatientsController extends DirectorController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|array
     */
    public function index()
    {
        return Patient::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePatient $request)
    {
        return Patient::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Patient::findOrFail($id)->toJson();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $request
     * @param $id
     * @return array
     */
    public function update(UpdatePatient $request, $id)
    {
        /** @var \Eloquent $status */
        $status = Patient::findOrFail($id);
        foreach ($status->getVisible() as $item) {
            if ($request->has($item)) {
                $status->$item = $request->get($item);
            }
        }
        $status->save();

        return ['success' => true];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return array
     */
    public function destroy($id)
    {
        Patient::destroy($id);

        return ['success' => true];
    }
}
