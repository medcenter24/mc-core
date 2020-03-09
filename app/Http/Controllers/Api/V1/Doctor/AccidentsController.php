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

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor;

use Dingo\Api\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\AccidentType;
use medcenter24\mcCore\App\Entity\Diagnostic;
use medcenter24\mcCore\App\Entity\Doctor;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Entity\Service;
use medcenter24\mcCore\App\Entity\Survey;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Http\Controllers\Api\ApiController;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\AccidentStatusesService;
use medcenter24\mcCore\App\Services\Entity\DoctorService;
use medcenter24\mcCore\App\Services\Entity\DocumentService;
use medcenter24\mcCore\App\Transformers\AccidentTypeTransformer;
use medcenter24\mcCore\App\Transformers\DiagnosticTransformer;
use medcenter24\mcCore\App\Transformers\DoctorAccidentStatusTransformer;
use medcenter24\mcCore\App\Transformers\DoctorAccidentTransformer;
use medcenter24\mcCore\App\Transformers\DoctorCaseAccidentTransformer;
use medcenter24\mcCore\App\Transformers\ServiceTransformer;
use medcenter24\mcCore\App\Transformers\SurveyTransformer;
use medcenter24\mcCore\App\Transformers\DocumentTransformer;
use medcenter24\mcCore\App\Transformers\PatientTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AccidentsController extends ApiController
{

    private $doctor;

    protected function getDoctor(): ?Doctor
    {
        if (!isset($this->doctor)) {
            $this->doctor = $this->user()->doctor;
            if (!$this->doctor) {
                $this->response->errorBadRequest('Current user should be a doctor');
            }
        }

        return $this->doctor;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $sort = explode('|', $request->get('sort', 'createdAt|desc'));
        switch ($sort[0]) {
            case 'status':
                $sort[0] = 'accident_statuses.title';
                break;
            case 'city':
                $sort[0] = 'cities.title';
                break;
            case 'refNum':
                $sort[0] = 'accidents.ref_num';
                break;
            case 'createdAt':
            default:
                $sort[0] = 'accidents.created_at';
        }

        $accidents = Accident::select('accidents.*')
            ->join('accident_statuses', 'accidents.accident_status_id', '=', 'accident_statuses.id')
            ->leftJoin('cities', 'accidents.city_id', '=', 'cities.id')
            ->leftJoin('doctor_accidents', 'accidents.caseable_id', '=', 'doctor_accidents.id')
            // doctors accidents only
            ->where('accidents.caseable_type', DoctorAccident::class)
            // doctors status
            ->where('accident_statuses.type', AccidentStatusesService::TYPE_DOCTOR)
            ->whereIn('accident_statuses.title', [
                AccidentStatusesService::STATUS_IN_PROGRESS,
                AccidentStatusesService::STATUS_ASSIGNED
            ])
            ->orderBy($sort[0], $sort[1])
            ->paginate($request->get('per_page', 10),
                $columns = ['*'], $pageName = 'page', $request->get('page', null));

        return $this->response->paginator($accidents, new DoctorCaseAccidentTransformer());
    }

    /**
     * Closed or accident which were sent which can't be changed
     * @param $id
     * @param DoctorService $doctorService
     * @param AccidentService $accidentService
     * @param AccidentStatusesService $accidentStatusesService
     * @return Response
     * @throws InconsistentDataException
     */
    public function show(
        int $id,
        DoctorService $doctorService,
        AccidentService $accidentService,
        AccidentStatusesService $accidentStatusesService
    ): Response
    {
        $accident = Accident::findOrFail($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        if (!$doctorService->hasAccess($this->getDoctor(), $accident)) {
            $this->response->errorNotFound();
        }

        $accidentService->setStatus($accident, $accidentStatusesService->getDoctorInProgressStatus());
        return $this->response->item($accident, new DoctorAccidentTransformer());
    }

    /**
     * @param $id
     * @return Response
     */
    public function patient($id): Response
    {
        $accident = Accident::find($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }
        $patient = $accident->patient;
        if (!$patient) {
            $this->response->errorNotFound();
        }

        return $this->response->item($patient, new PatientTransformer());
    }

    /**
     * @param $id
     * @param Request $request
     * @param AccidentService $accidentService
     * @param AccidentStatusesService $statusesService
     * @return Response
     * @throws InconsistentDataException
     */
    public function updatePatient($id, Request $request, AccidentService $accidentService, AccidentStatusesService $statusesService): Response
    {
        $accident = Accident::find($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        $patient = $accident->patient;
        if (!$patient) {
            $this->response->errorNotFound();
        }

        $changedData = [];

        $newName = $request->get('name','');
        $newComment = $request->get('comment', '');
        $newAddress = $request->get('address', '');

        if ($newName !== $patient->name) {
            $changedData['name'] = ['old' => $patient->name, 'new' => $newName];
            $patient->name = $newName;
        }

        if ($newComment !== $patient->comment) {
            $changedData['comment'] = ['old' => $accident->symptoms, 'new' => $newComment];
            $patient->comment = $newComment;
        }

        if ($newAddress !== $patient->address) {
            $changedData['address'] = ['old' => $patient->address, 'new' => $newAddress];
            $patient->address = $newAddress;
        }

        if (count($changedData)) {
            $status = $statusesService->getDoctorAssignedStatus();
            $accidentService->setStatus($accident, $status, 'Updated by doctor ' . $this->user()->id . ' ' . json_encode($changedData));
        }
        $patient->save();

        return $this->response->item($patient, new PatientTransformer());
    }

    /**
     * Get case status (new/old)
     * @param $id
     * @return Response
     */
    public function status($id): Response
    {
        $accident = Accident::find($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        $doctorAccident = $accident->caseable;
        if (!$doctorAccident || !is_a($doctorAccident, DoctorAccident::class)) {
            $this->response->errorNotFound();
        }

        return $this->response->item($doctorAccident, new DoctorAccidentStatusTransformer());
    }

    public function services($id): Response
    {
        $accident = Accident::find($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        /** @var Collection $services */
        $services = $accident->caseable->services->each(function (Service $service) {
            if ($service->created_by === $this->user()->id) {
                $service->markAsDoctor();
            }
        });

        return $this->response->collection($services, new ServiceTransformer());
    }

    public function saveService($id, Request $request): Response
    {
        Log::info('Request to create new services', ['data' => $request->toArray()]);
        $accident = Accident::find($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        $doctorAccident = $accident->caseable;

        $serviceId = (int)$request->get('id', 0);
        if ($serviceId) {
            $service = Service::find($serviceId);
            if (!$service) {
                Log::error('Service not found');
                $this->response->errorNotFound();
            }

            $serviceable = $service->serviceable();
            if (
                $serviceable->serviceable_id != $doctorAccident->id
                || $serviceable->serviceable_type != DoctorAccident::class
            ) {
                Log::error('Service can not be updated, user has not permissions');
                $this->response->errorMethodNotAllowed();
            }

            $service->title = $request->get('title', $service->title);
            $service->description = $request->get('decription', $service->description);
            $service->created_by = $this->user()->id;
            $service->save();
        } else {
            $service = Service::create([
                'title' => $request->get('title', ''),
                'description' => $request->get('description', ''),
                'created_by' => $this->user()->id,
            ]);
            $doctorAccident->services()->attach($service);
            $service->markAsDoctor();
        }

        $transformer = new ServiceTransformer();
        return $this->response->accepted(null, $transformer->transform($service));
    }

    public function type($id): Response
    {
        $accident = Accident::find($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        $accidentType = $accident->type;
        if (!$accidentType) {
            $accidentType = new AccidentType([
                'title' => 'Not Set',
                'description' => 'Accident type was not selected',
            ]);
        }

        return $this->response->item($accidentType, new AccidentTypeTransformer());
    }

    public function diagnostics($id): Response
    {
        $accident = Accident::find($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        /** @var Collection $diagnostics */
        $diagnostics = $accident->caseable->diagnostics->each(function (Diagnostic $diagnostic) {
            if ($diagnostic->created_by == $this->user()->id) {
                $diagnostic->markAsDoctor();
            }
        });

        return $this->response->collection($diagnostics, new DiagnosticTransformer());
    }

    public function createDiagnostic($id, Request $request)
    {
        Log::info('Request to create new diagnostic', ['data' => $request->toArray()]);
        $accident = Accident::find($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        $doctorAccident = $accident->caseable;

        $diagnosticId = $request->get('id', 0);
        if ($diagnosticId) {
            $diagnostic = Diagnostic::find($diagnosticId);
            if (!$diagnostic) {
                Log::error('Diagnostic not found');
                $this->response->errorNotFound();
            }

            $diagnosticable = $diagnostic->diagnosticable();
            if (
                $diagnosticable->diagnosticable_id != $accident->caseable->id
                || $diagnosticable->diagnosticable_type != DoctorAccident::class
            ) {
                Log::error('Diagnostic can not be updated, user has not permissions');
                $this->response->errorMethodNotAllowed();
            }

            $diagnostic->title = $request->get('title', $diagnostic->title);
            $diagnostic->description = $request->get('decription', $diagnostic->description);
            $diagnostic->created_by = $this->user()->id;
            $diagnostic->save();
        } else {
            $diagnostic = Diagnostic::create([
                'title' => $request->get('title', ''),
                'description' => $request->get('description', ''),
                'created_by' => $this->user()->id,
            ]);
            $doctorAccident->diagnostics()->attach($diagnostic);
            $diagnostic->markAsDoctor();
        }

        $transformer = new DiagnosticTransformer();
        return $this->response->accepted(null, $transformer->transform($diagnostic));
    }

    public function surveys($id)
    {
        $accident = Accident::find($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        /** @var Collection $surveys */
        $surveys = $accident->caseable->surveys->each(function (Survey $survey) {
            if ($survey->created_by == $this->user()->id) {
                $survey->markAsDoctor();
            }
        });

        return $this->response->collection($surveys, new SurveyTransformer());
    }

    public function createSurvey($id, Request $request)
    {
        Log::info('Request to create new survey', ['data' => $request->toArray()]);
        $accident = Accident::find($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        $doctorAccident = $accident->caseable;

        $surveyId = (int)$request->get('id', 0);
        if ($surveyId) {
            $survey = Diagnostic::find($surveyId);
            if (!$survey) {
                Log::error('Survey not found');
                $this->response->errorNotFound();
            }

            if (
                $survey->surveable_id != $doctorAccident->id
                || $survey->caseable_type != DoctorAccident::class
            ) {
                Log::error('Survey can not be updated, user has not permissions');
                $this->response->errorMethodNotAllowed();
            }

            $survey->title = $request->get('title', $survey->title);
            $survey->description = $request->get('decription', $survey->description);
            $survey->created_by = $this->user()->id;
            $survey->save();
        } else {
            $survey = Survey::create([
                'title' => $request->get('title', ''),
                'description' => $request->get('description', ''),
                'created_by' => $this->user()->id,
            ]);
            $doctorAccident->surveys()->attach($survey);
            $survey->markAsDoctor();
        }

        $transformer = new SurveyTransformer();
        return $this->response->accepted(null, $transformer->transform($survey));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        Log::info('Request to update accident', ['id' => $id, 'data' => $request->toArray()]);
        $accident = Accident::find($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        if (
            !$accident->accidentStatus->type === AccidentStatusesService::TYPE_DOCTOR
            || (
                !in_array($accident->accidentStatus->title, [
                    AccidentStatusesService::STATUS_ASSIGNED,
                    AccidentStatusesService::STATUS_IN_PROGRESS,
                ], false)
            )) {

            $this->response->errorMethodNotAllowed(trans('You cant\'t change this case'));
        }

        $accident->accident_type_id = (int)$request->json('caseType', 1);
        $accident->caseable->recommendation = (string)$request->json('diagnose', '');
        $accident->caseable->investigation = (string)$request->json('investigation', '');
        
        $visitTime = $request->json('visitDateTime', '');
        $time = Carbon::parse($visitTime);
        $accident->caseable->visit_time = $time->format('Y-m-d H:i:s');
        $accident->caseable->save();
        $accident->save();

        // exclude saved by director (to not change their color)
        $diagnostics = $request->json('diagnostics', []);
        $accident->caseable->diagnostics()->detach();
        $accident->caseable->diagnostics()->attach($diagnostics);
        $services = $request->json('services', []);
        $accident->caseable->services()->detach();
        $accident->caseable->services()->attach($services);
        $surveys = $request->json('surveys', []);
        $accident->caseable->surveys()->detach();
        $accident->caseable->surveys()->attach($surveys);

        return $this->response->noContent();
    }

    /**
     * Load documents into the accident
     * @param $id
     * @param Request $request
     * @param DocumentService $documentService
     * @return Response
     * @throws \Spatie\MediaLibrary\Exceptions\FileCannotBeAdded
     */
    public function createDocument($id, Request $request, DocumentService $documentService): Response
    {
        $accident = Accident::find($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }
        $document = $documentService->createDocumentFromFile(current($request->allFiles()), $this->user());
        $accident->documents()->attach($document);
        $accident->patient->documents()->attach($document);
        $doctorAccident = $accident->caseable;
        $doctorAccident->documents()->attach($document);
        return $this->response->item($document, new DocumentTransformer());
    }

    /**
     * @param $id
     * @param DocumentService $documentService
     * @return Response
     */
    public function documents($id, DocumentService $documentService): Response
    {
        $accident = Accident::find($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        return $this->response->collection(
            $documentService->getDocuments($this->user(), $accident, 'accident'),
            new DocumentTransformer()
        );
    }

    /**
     * @param $id
     * @param Request $request
     * @param AccidentStatusesService $accidentStatusesService
     * @param AccidentService $accidentService
     * @return Response
     * @throws InconsistentDataException
     */
    public function reject(
        $id,
        Request $request,
        AccidentStatusesService $accidentStatusesService,
        AccidentService $accidentService): Response
    {
        $accident = Accident::find($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        $accidentService->setStatus($accident, $accidentStatusesService->getDoctorRejectedStatus());
        return $this->response->noContent();
    }

    /**
     * Send cases to the director as completed
     * @param Request $request
     * @param AccidentService $accidentService
     * @param AccidentStatusesService $accidentStatusesService
     * @return Response
     * @throws InconsistentDataException
     */
    public function send(Request $request, AccidentService $accidentService, AccidentStatusesService $accidentStatusesService): Response
    {
        $accidents = $request->get('cases', []);

        if (!is_array($accidents) || !count($accidents)) {
            $this->response->errorBadRequest('Accidents do not provided');
        }

        foreach ($accidents as $accidentId) {
            $accident = Accident::find($accidentId);
            if (!$accident) {
                Log::warning('Accident has not been found, so it could not be sent to the doctor',
                    ['accidentId' => $accidentId, 'userId' => $this->user()->id]);
                continue;
            }
            $status = $accidentStatusesService->getDoctorSentStatus();
            $accidentService->setStatus($accident, $status, 'Sent by doctor');
        }

        return $this->response->noContent();
    }
}
