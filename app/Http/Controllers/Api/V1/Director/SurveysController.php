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

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director;

use Dingo\Api\Http\Response;
use medcenter24\mcCore\App\DoctorSurvey;
use medcenter24\mcCore\App\Http\Controllers\ApiController;
use medcenter24\mcCore\App\Http\Requests\Api\DoctorSurveyRequest;
use medcenter24\mcCore\App\Transformers\DoctorSurveyTransformer;
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

    public function update($id, DoctorSurveyRequest $request): Response
    {
        /** @var DoctorSurvey $doctorSurvey */
        $doctorSurvey = DoctorSurvey::find($id);
        if (!$doctorSurvey) {
            $this->response->errorNotFound();
        }

        $doctorSurvey->title= $request->json('title', '');
        $doctorSurvey->description = $request->json('description', '');
        $doctorSurvey->created_by = $this->user()->id;
        $doctorSurvey->setAttribute('status', $request->json('status', 'active'));
        $doctorSurvey->save();

        $transformer = new DoctorSurveyTransformer();
        return $this->response->accepted(null, $transformer->transform($doctorSurvey));
    }

    public function store(DoctorSurveyRequest $request): Response
    {
        $doctorSurvey = DoctorSurvey::create([
            'title' => $request->json('title', ''),
            'description' => $request->json('description', ''),
            'created_by' => $this->user()->id,
            'status' => $request->json('status', 'active'),
        ]);
        $transformer = new DoctorSurveyTransformer();
        return $this->response->created(null, $transformer->transform($doctorSurvey));
    }
    
    public function destroy($id): Response
    {
        $service = DoctorSurvey::find($id);
        if (!$service) {
            $this->response->errorNotFound();
        }
        $service->delete();
        return $this->response->noContent();
    }
}
