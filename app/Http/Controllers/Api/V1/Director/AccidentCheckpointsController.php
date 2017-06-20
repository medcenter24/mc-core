<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\AccidentCheckpoint;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\AccidentCheckpointRequest;
use App\Transformers\AccidentCheckpointTransformer;

class AccidentCheckpointsController extends ApiController
{
    public function index()
    {
        $accidentCheckpoint = AccidentCheckpoint::orderBy('title')->get();
        return $this->response->collection($accidentCheckpoint, new AccidentCheckpointTransformer());
    }

    public function show($id)
    {
        $accidentCheckpoint = AccidentCheckpoint::findOrFail($id);
        return $this->response->item($accidentCheckpoint, new AccidentCheckpointTransformer());
    }

    public function store(AccidentCheckpointRequest $request)
    {
        $accidentCheckpoint = AccidentCheckpoint::create([
            'title' => $request->json('title', ''),
            'description' => $request->json('description', ''),
        ]);
        $transformer = new AccidentCheckpointTransformer();
        return $this->response->created(null, $transformer->transform($accidentCheckpoint));
    }

    public function update($id, AccidentCheckpointRequest $request)
    {
        $accidentCheckpoint = AccidentCheckpoint::findOrFail($id);
        $accidentCheckpoint->title = $request->json('title', '');
        $accidentCheckpoint->description = $request->json('description', '');
        $accidentCheckpoint->save();
        \Log::info('Accident status updated', [$accidentCheckpoint, $this->user()]);
        $this->response->item($accidentCheckpoint, new AccidentCheckpointTransformer());
    }

    public function destroy($id)
    {
        $accidentCheckpoint = AccidentCheckpoint::findOrFail($id);
        \Log::info('Accident status deleted', [$accidentCheckpoint, $this->user()]);
        $accidentCheckpoint->delete();
        return $this->response->noContent();
    }
}
