<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Doctor;

use App\Accident;
use App\AccidentStatus;
use App\Diagnostic;
use App\Doctor;
use App\DoctorAccident;
use App\DoctorService;
use App\DoctorSurvey;
use App\Document;
use App\Http\Controllers\ApiController;
use App\Services\AccidentStatusesService;
use App\Services\DoctorsService;
use App\Services\DocumentService;
use App\Transformers\AccidentTransformer;
use App\Transformers\AccidentTypeTransformer;
use App\Transformers\CaseAccidentTransformer;
use App\Transformers\DiagnosticTransformer;
use App\Transformers\DoctorAccidentStatusTransformer;
use App\Transformers\DoctorAccidentTransformer;
use App\Transformers\DoctorServiceTransformer;
use App\Transformers\DoctorSurveyTransformer;
use App\Transformers\DocumentTransformer;
use App\Transformers\PatientTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AccidentsController extends ApiController
{

    private $doctor;

    protected function getDoctor()
    {
        if (!isset($this->doctor)) {
            $this->doctor = $this->user()->doctor;
            if (!$this->doctor) {
                $this->response->errorForbidden('Current user should be a doctor');
            }
        }

        return $this->doctor;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort = explode('|', $request->get('sort', 'created_at|desc'));
        switch ($sort[0]) {
            case 'status':
                $sort[0] = 'accident_statuses.title';
                break;
            case 'city':
                $sort[0] = 'cities.title';
                break;
            case 'ref_num':
                $sort[0] = 'accidents.ref_num';
                break;
            case 'created_at':
            default:
                $sort[0] = 'accidents.created_at';
        }

        $accidents = Accident::select('accidents.*')
            ->join('accident_statuses', 'accidents.accident_status_id', '=', 'accident_statuses.id')
            ->leftJoin('cities', 'accidents.city_id', '=', 'cities.id')
            ->where('accidents.caseable_type', DoctorAccident::class)
            ->whereIn('accidents.caseable_id', DoctorAccident::where('doctor_id', $this->getDoctor()->id)->pluck('id')->toArray())
            ->where('accident_statuses.type', \AccidentStatusesTableSeeder::TYPE_DOCTOR)
            ->whereIn('accident_statuses.title', [
                \AccidentStatusesTableSeeder::STATUS_IN_PROGRESS,
                \AccidentStatusesTableSeeder::STATUS_ASSIGNED
            ])
            ->orderBy($sort[0], $sort[1])
            ->paginate($request->get('per_page', 10),
                $columns = ['*'], $pageName = 'page', $request->get('page', null));

        return $this->response->paginator($accidents, new CaseAccidentTransformer());
    }

    /**
     * Closed or accident which were sent which can't be changed
     *
     * @param  int  $id
     * @param DoctorsService $doctorService
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id, DoctorsService $doctorService, AccidentStatusesService $accidentStatusesService)
    {
        $accident = Accident::find($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        if (!$doctorService->hasAccess($this->getDoctor(), $accident)) {
            $this->response->errorNotFound();
        }

        if ($accident->accidentStatus->title == AccidentStatusesService::STATUS_ASSIGNED
            && $accident->accidentStatus->type == AccidentStatusesService::TYPE_DOCTOR) {

            $status = AccidentStatus::firstOrCreate([
                'title' => AccidentStatusesService::STATUS_IN_PROGRESS,
                'type' => AccidentStatusesService::TYPE_DOCTOR,
            ]);

            $accidentStatusesService->set($accident, $status, 'Updated by doctor');
        }

        return $this->response->item($accident, new DoctorAccidentTransformer());
    }

    public function patient($id)
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

    public function updatePatient($id, Request $request, AccidentStatusesService $statusesService)
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

        if ($newName != $patient->name) {
            $changedData['name'] = ['old' => $patient->name, 'new' => $newName];
            $patient->name = $newName;
        }

        if ($newComment != $patient->comment) {
            $changedData['comment'] = ['old' => $accident->symptoms, 'new' => $newComment];
            $patient->comment = $newComment;
        }

        if ($newAddress != $patient->address) {
            $changedData['address'] = ['old' => $patient->address, 'new' => $newAddress];
            $patient->address = $newAddress;
        }

        if (count($changedData)) {
            $statusesService->set($accident, AccidentStatus::firstOrCreate([
                'title' => AccidentStatusesService::STATUS_ASSIGNED,
                'type' => AccidentStatusesService::TYPE_DOCTOR,
            ]), 'Updated by doctor ' . $this->user()->id . ' ' . json_encode($changedData));
        }
        $patient->save();

        return $this->response->item($patient, new PatientTransformer());
    }

    public function status($id)
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

    public function services($id)
    {
        $accident = Accident::find($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        /** @var \Illuminate\Support\Collection $services */
        $services = $accident->caseable->services->each(function (DoctorService $service) {
            $service->markAsDoctor();
        });
        $services = $services->merge($accident->services)->reverse();

        return $this->response->collection($services, new DoctorServiceTransformer());
    }

    public function saveService($id, Request $request)
    {
        \Log::info('Request to create new services', ['data' => $request->toArray()]);
        $accident = Accident::find($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        $doctorAccident = $accident->caseable;

        $serviceId = $request->get('id', 0);
        if ($serviceId) {
            $service = DoctorService::find($serviceId);
            if (!$service) {
                \Log::error('Diagnostic not found');
                $this->response->errorNotFound();
            }

            $serviceable = $service->serviceable();
            if (
                $serviceable->serviceable_id != $doctorAccident->id
                || $serviceable->serviceable_type != DoctorAccident::class
            ) {
                \Log::error('Service can not be updated, user has not permissions');
                $this->response->errorMethodNotAllowed();
            }

            $service->title = $request->get('title', $service->title);
            $service->description = $request->get('decription', $service->description);
            $service->save();
        } else {
            $service = DoctorService::create([
                'title' => $request->get('title', ''),
                'description' => $request->get('description', '')
            ]);
            $doctorAccident->services()->attach($service);
            $service->markAsDoctor();
        }

        $transformer = new DoctorServiceTransformer();
        return $this->response->accepted(null, $transformer->transform($service));
    }

    public function type($id)
    {
        $accident = Accident::find($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        return $this->response->item($accident->type, new AccidentTypeTransformer());
    }

    public function diagnostics($id)
    {
        $accident = Accident::find($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        /** @var \Illuminate\Support\Collection $diagnostics */
        $diagnostics = $accident->caseable->diagnostics->each(function (Diagnostic $diagnostic) {
            $diagnostic->markAsDoctor();
        });
        $diagnostics = $diagnostics->merge($accident->diagnostics)->reverse();

        return $this->response->collection($diagnostics, new DiagnosticTransformer());
    }

    public function createDiagnostic($id, Request $request)
    {
        \Log::info('Request to create new diagnostic', ['data' => $request->toArray()]);
        $accident = Accident::find($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        $doctorAccident = $accident->caseable;

        $diagnosticId = $request->get('id', 0);
        if ($diagnosticId) {
            $diagnostic = Diagnostic::find($diagnosticId);
            if (!$diagnostic) {
                \Log::error('Diagnostic not found');
                $this->response->errorNotFound();
            }

            $diagnosticable = $diagnostic->diagnosticable();
            if (
                $diagnosticable->diagnosticable_id != $accident->caseable->id
                || $diagnosticable->diagnosticable_type != DoctorAccident::class
            ) {
                \Log::error('Diagnostic can not be updated, user has not permissions');
                $this->response->errorMethodNotAllowed();
            }

            $diagnostic->title = $request->get('title', $diagnostic->title);
            $diagnostic->description = $request->get('decription', $diagnostic->description);
            $diagnostic->save();
        } else {
            $diagnostic = Diagnostic::create([
                'title' => $request->get('title', ''),
                'description' => $request->get('description', '')
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
        $surveys = $accident->caseable->surveys->each(function (DoctorSurvey $survey) {
            $survey->markAsDoctor();
        });
        $surveys = $surveys->merge($accident->surveys)->reverse();

        return $this->response->collection($surveys, new DoctorSurveyTransformer());
    }

    public function createSurvey($id, Request $request)
    {
        \Log::info('Request to create new survey', ['data' => $request->toArray()]);
        $accident = Accident::find($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        $doctorAccident = $accident->caseable;

        $surveyId = $request->get('id', 0);
        if ($surveyId) {
            $survey = Diagnostic::find($surveyId);
            if (!$survey) {
                \Log::error('Diagnostic not found');
                $this->response->errorNotFound();
            }

            if (
                $survey->surveable_id != $doctorAccident->id
                || $survey->caseable_type != DoctorAccident::class
            ) {
                \Log::error('Survey can not be updated, user has not permissions');
                $this->response->errorMethodNotAllowed();
            }

            $survey->title = $request->get('title', $survey->title);
            $survey->description = $request->get('decription', $survey->description);
            $survey->save();
        } else {
            $survey = DoctorSurvey::create([
                'title' => $request->get('title', ''),
                'description' => $request->get('description', ''),
                'created_by' => $this->user()->id,
            ]);
            $doctorAccident->surveys()->attach($survey);
            $survey->markAsDoctor();
        }

        $transformer = new DoctorSurveyTransformer();
        return $this->response->accepted(null, $transformer->transform($survey));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        \Log::info('Request to update accident', ['id' => $id, 'data' => $request->toArray()]);
        $accident = Accident::find($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        if (
            !$accident->accidentStatus->type == \AccidentStatusesTableSeeder::TYPE_DOCTOR
            || (
                !in_array($accident->accidentStatus->title, [
                    \AccidentStatusesTableSeeder::STATUS_ASSIGNED,
                    \AccidentStatusesTableSeeder::STATUS_IN_PROGRESS,
                ])
            )) {

            $this->response->errorMethodNotAllowed(trans('You cant\'t change this case'));
        }

        $accident->accident_type_id = (int)$request->json('caseType', 1);
        $accident->caseable->recommendation = (string)$request->json('diagnose', '');
        $accident->caseable->investigation = (string)$request->json('investigation', '');
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
     * @param int $id
     * @param Request $request
     * @param DocumentService $documentService
     * @return \Dingo\Api\Http\Response
     */
    public function createDocument($id, Request $request, DocumentService $documentService)
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

    public function documents($id, DocumentService $documentService)
    {
        $accident = Accident::find($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        return $this->response->collection($documentService->getDocuments($this->user(), $accident), new DocumentTransformer());
    }

    public function reject($id, Request $request, AccidentStatusesService $accidentStatusesService)
    {
        $accident = Accident::find($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        $status = AccidentStatus::firstOrCreate([
            'title' => AccidentStatusesService::STATUS_REJECT,
            'type' => AccidentStatusesService::TYPE_DOCTOR,
        ]);

        $accidentStatusesService->set($accident, $status, $request->get('comment', 'Updated by doctor without commentary'));

        return $this->response->noContent();
    }

    /**
     * Send cases to the director as completed
     * @param Request $request
     * @param AccidentStatusesService $accidentStatusesService
     * @return \Dingo\Api\Http\Response
     */
    public function send(Request $request, AccidentStatusesService $accidentStatusesService)
    {
        $accidents = $request->get('cases', []);

        if (!is_array($accidents) || !count($accidents)) {
            $this->response->errorBadRequest('Accidents do not provided');
        }

        foreach ($accidents as $accidentId) {
            $accident = Accident::find($accidentId);
            if (!$accident) {
                \Log::warning('Accident has not been found, so it could not be sent to the doctor',
                    ['accidentId' => $accidentId, 'userId' => $this->user()->id]);
                continue;
            }
            $status = AccidentStatus::firstOrCreate([
                'title' => AccidentStatusesService::STATUS_SENT,
                'type' => AccidentStatusesService::TYPE_DOCTOR,
            ]);
            $accidentStatusesService->set($accident, $status, 'Sent by doctor');
        }

        return $this->response->noContent();
    }
}
