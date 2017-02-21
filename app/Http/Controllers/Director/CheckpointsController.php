<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Director;

use App\AccidentCheckpoint;
use App\Http\Controllers\DirectorController;
use App\Http\Requests\StoreAccidentCheckpoint;
use App\Http\Requests\UpdateAccidentCheckpoint;

/**
 * Class CheckpointsController
 * @package App\Http\Controllers\Director
 */
class CheckpointsController extends DirectorController
{
    public function index()
    {
        return AccidentCheckpoint::all();
    }

    public function store(StoreAccidentCheckpoint $request)
    {
        return AccidentCheckpoint::create($request->all());
    }

    public function show($id)
    {
        return AccidentCheckpoint::findOrFail($id)->toJson();
    }

    public function update(UpdateAccidentCheckpoint $request, $id)
    {
        /** @var \Eloquent $status */
        $status = AccidentCheckpoint::findOrFail($id);
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
        AccidentCheckpoint::destroy($id);

        return ['success' => true];
    }
}
