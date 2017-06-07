<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\Accident;
use App\Http\Controllers\ApiController;
use App\Http\Requests\StoreAccident;
use App\Http\Requests\UpdateAccident;
use App\Transformers\AccidentTransformer;

class AccidentsController extends ApiController
{

    public function index()
    {
        $accidents = Accident::orderBy('created_at', 'desc')->get();
        return $this->response->collection($accidents, new AccidentTransformer());
    }

    public function store(StoreAccident $request)
    {
        return Accident::create($request->all());
    }

    public function show($id)
    {
        $accident = Accident::find($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        return $this->response->item($accident, new AccidentTransformer());
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
