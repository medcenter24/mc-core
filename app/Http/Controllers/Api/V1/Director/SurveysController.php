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

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director;

use Dingo\Api\Http\Response;
use medcenter24\mcCore\App\DoctorSurvey;
use medcenter24\mcCore\App\Http\Controllers\ApiController;
use medcenter24\mcCore\App\Http\Requests\Api\DoctorSurveyRequest;
use medcenter24\mcCore\App\Services\DoctorSurveyService;
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

        $fields = collect(DoctorSurveyService::FILLABLE);
        $fields->filter(static function ($field) use ($doctorSurvey, $request) {
            if ($field !== DoctorSurveyService::FIELD_CREATED_BY && $request->has($field)) {
                $doctorSurvey->setAttribute($field, $request->get($field));
            }
        });
        $doctorSurvey->save();

        $transformer = new DoctorSurveyTransformer();
        return $this->response->accepted(null, $transformer->transform($doctorSurvey));
    }

    public function store(DoctorSurveyRequest $request, DoctorSurveyService $doctorSurveyService): Response
    {
        $data = $request->json()->all();
        $doctorSurvey = $doctorSurveyService->create([
            DoctorSurveyService::FIELD_TITLE => $data['title'],
            DoctorSurveyService::FIELD_DESCRIPTION => $data['description'],
            DoctorSurveyService::FIELD_DISEASE_ID => $data['diseaseId'],
            DoctorSurveyService::FIELD_CREATED_BY => $this->user()->id,
            DoctorSurveyService::FIELD_STATUS => DoctorSurveyService::STATUS_ACTIVE,
        ]);
        $doctorSurvey->save();
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
