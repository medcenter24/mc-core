<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2020 (original work) MedCenter24.com;
 */

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor\Accident;

use Dingo\Api\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Entity\Survey;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Http\Controllers\Api\ApiController;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\SurveyService;
use medcenter24\mcCore\App\Transformers\SurveyTransformer;

class SurveysAccidentController extends ApiController
{
    use DoctorAccidentControllerTrait;

    /**
     * @return AccidentService
     */
    private function getAccidentService(): AccidentService
    {
        return $this->getServiceLocator()->get(AccidentService::class);
    }

    /**
     * @return SurveyService
     */
    private function getSurveyService(): SurveyService
    {
        return $this->getServiceLocator()->get(SurveyService::class);
    }

    /**
     * @param $id
     * @return Response
     */
    public function surveys($id): Response
    {
        /** @var Accident $accident */
        $accident = $this->getAccidentService()->first([AccidentService::FIELD_ID => $id]);
        $this->checkAccess($accident);

        /** @var Collection $surveys */
        $surveys = $accident->caseable->surveys->each(function (Survey $survey) {
            if ($survey->created_by === $this->user()->id) {
                $survey->markAsDoctor();
            }
        });

        return $this->response->collection($surveys, new SurveyTransformer());
    }

    /**
     * @param $id
     * @param Request $request
     * @return Response
     * @throws InconsistentDataException
     */
    public function createSurvey($id, Request $request)
    {
        Log::info('Request to create new survey', ['data' => $request->toArray()]);

        /** @var Accident $accident */
        $accident = $this->getAccidentService()->first([AccidentService::FIELD_ID => $id]);
        $this->checkAccess($accident);

        $doctorAccident = $accident->caseable;

        $surveyId = (int) $request->get('id', 0);
        if ($surveyId) {
            /** @var Survey $survey */
            $survey = $this->getSurveyService()->first([SurveyService::FIELD_ID => $surveyId]);
            if (!$survey) {
                Log::error('Survey not found');
                $this->response->errorNotFound();
            }

            if (!$this->getSurveyService()->hasAccess($this->user(), $survey)) {
                Log::error('Survey can not be updated, user has not permissions');
                $this->response->errorMethodNotAllowed();
            }

            $survey = $this->getSurveyService()->findAndUpdate([SurveyService::FIELD_ID], [
                SurveyService::FIELD_ID => $surveyId,
                SurveyService::FIELD_TITLE => $request->get('title', $survey->title),
                SurveyService::FIELD_DESCRIPTION => $request->get('description', $survey->description),
                SurveyService::FIELD_DISEASE_ID => $request->get('diseaseId', 0),
                SurveyService::FIELD_STATUS => $request->get('status', SurveyService::STATUS_ACTIVE),
            ]);
        } else {
            $survey = $this->getSurveyService()->create([
                SurveyService::FIELD_TITLE => $request->get('title', ''),
                SurveyService::FIELD_DESCRIPTION => $request->get('description', ''),
                SurveyService::FIELD_CREATED_BY => $this->user()->id,
                SurveyService::FIELD_DISEASE_ID => $request->get('diseaseId', 0),
                SurveyService::FIELD_STATUS => $request->get('status', SurveyService::STATUS_ACTIVE),
            ]);
            $doctorAccident->surveys()->attach($survey);
            $doctorAccident->save();
            $survey->markAsDoctor();
        }

        $transformer = new SurveyTransformer();
        return $this->response->accepted(null, $transformer->transform($survey));
    }
}
