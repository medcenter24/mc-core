<?php
/**
 * Copyright (c) 2017. 
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Director;


use App\Form;
use App\Http\Controllers\DirectorController;
use App\Http\Requests\StoreForm;
use App\Http\Requests\UpdateForm;

class FormsController extends DirectorController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|array
     */
    public function index()
    {
        return Form::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreForm $request)
    {
        return Form::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Form::findOrFail($id)->toJson();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $request
     * @param $id
     * @return array
     */
    public function update(UpdateForm $request, $id)
    {
        /** @var \Eloquent $status */
        $status = Form::findOrFail($id);
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
        Form::destroy($id);

        return ['success' => true];
    }
}
