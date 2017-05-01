<?php
/**
 * Copyright (c) 2017. 
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Director;


use App\Diagnostic;
use App\Http\Controllers\DirectorController;
use App\Http\Requests\StoreDiagnostic;
use App\Http\Requests\UpdateDiagnostic;

class DiagnosticsController extends DirectorController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|array
     */
    public function index()
    {
        return Diagnostic::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDiagnostic $request)
    {
        return Diagnostic::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Diagnostic::findOrFail($id)->toJson();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $request
     * @param $id
     * @return array
     */
    public function update(UpdateDiagnostic $request, $id)
    {
        /** @var \Eloquent $status */
        $status = Diagnostic::findOrFail($id);
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
        Diagnostic::destroy($id);

        return ['success' => true];
    }
}