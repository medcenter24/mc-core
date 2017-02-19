<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Director;


use App\AccidentStatus;
use App\Http\Requests\AccidentStatusRequest;
use App\Http\Requests\StoreAccidentStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccidentStatusController extends Controller
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
