<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Director;


use App\AccidentStatus;
use App\Http\Controllers\DirectorController;
use App\Http\Requests\StoreAccidentStatus;
use App\Http\Requests\UpdateAccidentStatus;

class StatusesController extends DirectorController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|array
     */
    public function index()
    {
        return AccidentStatus::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAccidentStatus $request)
    {
        return AccidentStatus::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return AccidentStatus::findOrFail($id)->toJson();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAccidentStatus $request
     * @param $id
     * @return array
     */
    public function update(UpdateAccidentStatus $request, $id)
    {
        /** @var \Eloquent $status */
        $status = AccidentStatus::findOrFail($id);
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
        AccidentStatus::destroy($id);

        return ['success' => true];
    }
}
