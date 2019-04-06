<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) MedCenter24.com;
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
