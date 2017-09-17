<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\AccidentStatus;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\AccidentStatusRequest;
use App\Transformers\AccidentStatusTransformer;

class AccidentStatusesController extends ApiController
{
    public function index()
    {
        $accidentStatus = AccidentStatus::orderBy('title')->get();
        return $this->response->collection($accidentStatus, new AccidentStatusTransformer());
    }

    public function show($id)
    {
        $accidentStatus = AccidentStatus::findOrFail($id);
        return $this->response->item($accidentStatus, new AccidentStatusTransformer());
    }

    public function store(AccidentStatusRequest $request)
    {
        $accidentStatus = AccidentStatus::create([
            'title' => $request->json('title', ''),
            'type' => $request->json('type', ''),
        ]);
        $transformer = new AccidentStatusTransformer();
        return $this->response->created(null, $transformer->transform($accidentStatus));
    }

    public function update($id, AccidentStatusRequest $request)
    {
        $accidentStatus = AccidentStatus::findOrFail($id);
        $accidentStatus->title = $request->json('title', '');
        $accidentStatus->type = $request->json('type', '');
        $accidentStatus->save();
        \Log::info('Accident status updated', [$accidentStatus, $this->user()]);
        $this->response->item($accidentStatus, new AccidentStatusTransformer());
    }

    public function destroy($id)
    {
        $accidentStatus = AccidentStatus::findOrFail($id);
        \Log::info('Accident status deleted', [$accidentStatus, $this->user()]);
        $accidentStatus->delete();
        return $this->response->noContent();
    }
}
