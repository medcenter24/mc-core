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
use League\Fractal\TransformerAbstract;

class DiagnosticsController extends ApiController
{
    protected function getDataTransformer(): TransformerAbstract
    {
        return new DiagnosticTransformer();
    }

    protected function getModelClass(): string
    {
        return Diagnostic::class;
    }

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
        $diagnostic->disease_code = $request->json('diseaseCode', '');
        $diagnostic->description = $request->json('description', '');
        $diagnostic->diagnostic_category_id = $request->json('diagnosticCategoryId', 0);
        $diagnostic->created_by = $this->user()->id;
        $diagnostic->save();

        $transformer = new DiagnosticTransformer();
        return $this->response->accepted(null, $transformer->transform($diagnostic));
    }

    public function store(DiagnosticRequest $request)
    {
        $diagnostic = Diagnostic::create([
            'title' => $request->json('title', ''),
            'disease_code' => $request->json('diseaseCode', ''),
            'description' => $request->json('description', ''),
            'category_id' => $request->json('diagnosticCategoryId', 0),
            'created_by' => $this->user()->id,
        ]);
        $transformer = new DiagnosticTransformer();
        return $this->response->created(null, $transformer->transform($diagnostic));
    }

    public function destroy($id)
    {
        $diagnostic = Diagnostic::find($id);
        if (!$diagnostic) {
            $this->response->errorNotFound();
        }
        $diagnostic->delete();
        return $this->response->noContent();
    }
}
