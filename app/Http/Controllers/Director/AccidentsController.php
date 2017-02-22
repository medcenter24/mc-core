<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Director;

use App\Accident;
use App\Http\Controllers\DirectorController;
use App\Http\Requests\StoreAccident;
use App\Http\Requests\UpdateAccident;

/**
 * @package App\Http\Controllers\Director
 */
class AccidentsController extends DirectorController
{

    public function index()
    {
        return Accident::all();
    }

    public function store(StoreAccident $request)
    {
        return Accident::create($request->all());
    }

    public function show($id)
    {
        return Accident::findOrFail($id)->toJson();
    }

    public function update(UpdateAccident $request, $id)
    {
        /** @var \Eloquent $status */
        $status = Accident::findOrFail($id);
        foreach ($status->getVisible() as $item) {
            if ($request->has($item)) {
                $status->$item = $request->get($item);
            }
        }
        $status->save();

        return ['success' => true];
    }

    public function destroy($id)
    {
        Accident::destroy($id);

        return ['success' => true];
    }
}
