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

use App\DoctorSurvey;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\DoctorSurveyRequest;
use App\Transformers\DoctorSurveyTransformer;
use League\Fractal\TransformerAbstract;

class SurveysController extends ApiController
{
    protected function getDataTransformer(): TransformerAbstract
    {
        return new DoctorSurveyTransformer();
    }

    protected function getModelClass(): string
    {
        return DoctorSurvey::class;
    }

    public function index()
    {
        $services = DoctorSurvey::orderBy('title', 'desc')->get();
        return $this->response->collection($services, new DoctorSurveyTransformer());
    }

    public function update($id, DoctorSurveyRequest $request)
    {
        $doctorService = DoctorSurvey::find($id);
        if (!$doctorService) {
            $this->response->errorNotFound();
        }

        $doctorService->title= $request->json('title', '');
        $doctorService->description = $request->json('description', '');
        $doctorService->created_by = $this->user()->id;
        $doctorService->save();

        $transformer = new DoctorSurveyTransformer();
        return $this->response->accepted(null, $transformer->transform($doctorService));
    }

    public function store(DoctorSurveyRequest $request)
    {
        $doctorService = DoctorSurvey::create([
            'title' => $request->json('title', ''),
            'description' => $request->json('description', ''),
            'created_by' => $this->user()->id,
        ]);
        $transformer = new DoctorSurveyTransformer();
        return $this->response->created(null, $transformer->transform($doctorService));
    }
    
    public function destroy($id)
    {
        $service = DoctorSurvey::find($id);
        if (!$service) {
            $this->response->errorNotFound();
        }
        $service->delete();
        return $this->response->noContent();
    }
}
