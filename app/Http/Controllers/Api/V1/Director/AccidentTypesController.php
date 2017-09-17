<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\AccidentType;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\AccidentTypeRequest;
use App\Transformers\AccidentTypeTransformer;

class AccidentTypesController extends ApiController
{
    public function index()
    {
        $types = AccidentType::orderBy('title')->get();
        return $this->response->collection($types, new AccidentTypeTransformer());
    }

    public function show($id)
    {
        $accidentType = AccidentType::findOrFail($id);
        return $this->response->item($accidentType, new AccidentTypeTransformer());
    }

    public function store(AccidentTypeRequest $request)
    {
        $accidentType = AccidentType::create([
            'title' => $request->json('title', ''),
            'description' => $request->json('description', ''),
        ]);
        $transformer = new AccidentTypeTransformer();
        return $this->response->created(null, $transformer->transform($accidentType));
    }

    public function update($id, AccidentTypeRequest $request)
    {
        $accidentType = AccidentType::findOrFail($id);
        $accidentType->title = $request->json('title', '');
        $accidentType->description = $request->json('description', '');
        $accidentType->save();
        \Log::info('Accident type updated', [$accidentType, $this->user()]);
        $this->response->item($accidentType, new AccidentTypeTransformer());
    }

    public function destroy($id)
    {
        $accidentType = AccidentType::findOrFail($id);
        \Log::info('Accident type deleted', [$accidentType, $this->user()]);
        $accidentType->delete();
        return $this->response->noContent();
    }
}
