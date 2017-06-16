<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\Diagnostic;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\DiagnosticRequest;
use App\Transformers\DiagnosticTransformer;

class DiagnosticsController extends ApiController
{
    public function index()
    {
        $diagnostics = Diagnostic::orderBy('title')->get();
        return $this->response->collection($diagnostics, new DiagnosticTransformer());
    }

    public function update($id, DiagnosticRequest $request)
    {
        $diagnostic = Diagnostic::find($id);
        if (!$diagnostic) {
            $this->response->errorNotFound();
        }

        $diagnostic->title= $request->json('title', '');
        $diagnostic->disease_code = $request->json('disease_code', '');
        $diagnostic->description = $request->json('description', '');
        $diagnostic->diagnostic_category_id = $request->json('diagnostic_category_id', 0);
        $diagnostic->save();

        $transformer = new DiagnosticTransformer();
        return $this->response->accepted(null, $transformer->transform($diagnostic));
    }

    public function store(DiagnosticRequest $request)
    {
        $diagnostic = Diagnostic::create([
            'title' => $request->json('title', ''),
            'disease_code' => $request->json('disease_code', ''),
            'description' => $request->json('description', ''),
            'category_id' => $request->json('diagnostic_category_id', 0),
        ]);
        $transformer = new DiagnosticTransformer();
        return $this->response->created(null, $transformer->transform($diagnostic));
    }
}
