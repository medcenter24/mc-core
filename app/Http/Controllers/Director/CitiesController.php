<?php
/**
 * Copyright (c) 2017. 
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Director;


use App\City;
use App\Http\Controllers\DirectorController;
use App\Http\Requests\StoreCity;
use App\Http\Requests\UpdateCity;

class CitiesController extends DirectorController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|array
     */
    public function index()
    {
        return City::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCity $request)
    {
        return City::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return City::findOrFail($id)->toJson();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $request
     * @param $id
     * @return array
     */
    public function update(UpdateCity $request, $id)
    {
        /** @var \Eloquent $status */
        $status = City::findOrFail($id);
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
        City::destroy($id);

        return ['success' => true];
    }
}
