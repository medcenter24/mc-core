<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\Assistant;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\AssistantRequest;
use App\Transformers\AssistantTransformer;
use App\Transformers\ModelTransformer;

class AssistantsController extends ApiController
{
    public function index()
    {
        return $this->response->collection(
            Assistant::orderBy('title')->get(),
            new AssistantTransformer()
        );
    }

    public function show($id)
    {
        $assistant = Assistant::findOrFail($id);
        return $this->response->item($assistant, new AssistantTransformer());
    }

    public function store(AssistantRequest $request)
    {
        $assistant = Assistant::create([
            'title' => $request->json('title', ''),
            'ref_key' => $request->json('ref_key', ''),
            'email' => $request->json('email', ''),
            'comment' => $request->json('comment', ''),
        ]);
        $transformer = new AssistantTransformer();
        return $this->response->created(null, $transformer->transform($assistant));
    }

    public function update($id, AssistantRequest $request)
    {
        $assistant = Assistant::findOrFail($id);
        $assistant->title = $request->json('title', '');
        $assistant->ref_key = $request->json('ref_key', '');
        $assistant->email = $request->json('email', '');
        $assistant->comment = $request->json('comment', '');
        $assistant->save();
        \Log::info('Assistant updated', [$assistant, $this->user()]);
        $this->response->item($assistant, new AssistantTransformer());
    }

    public function destroy($id)
    {
        $assistant = Assistant::findOrFail($id);
        \Log::info('Assistant deleted', [$assistant, $this->user()]);
        $assistant->delete();
        return $this->response->noContent();
    }
}
