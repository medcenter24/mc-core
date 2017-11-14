<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\DoctorSurvey;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\DoctorSurveyRequest;
use App\Transformers\DoctorSurveyTransformer;

class SurveysController extends ApiController
{
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
