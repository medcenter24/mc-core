<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\Accident;
use App\AccidentStatus;
use App\Diagnostic;
use App\Discount;
use App\DoctorAccident;
use App\DoctorService;
use App\DoctorSurvey;
use App\Events\DoctorAccidentUpdatedEvent;
use App\Http\Controllers\ApiController;
use App\Patient;
use App\Services\AccidentService;
use App\Services\AccidentStatusesService;
use App\Services\CaseServices\CaseHistoryService;
use App\Services\CaseServices\CaseReportService;
use App\Services\DocumentService;
use App\Services\Messenger\ThreadService;
use App\Services\ReferralNumberService;
use App\Services\RoleService;
use App\Services\Scenario\DoctorScenarioService;
use App\Services\Scenario\ScenarioService;
use App\Services\Scenario\StoryService;
use App\Services\ScenarioInterface;
use App\Transformers\AccidentCheckpointTransformer;
use App\Transformers\AccidentStatusHistoryTransformer;
use App\Transformers\CaseAccidentTransformer;
use App\Transformers\DiagnosticTransformer;
use App\Transformers\DirectorCaseTransformer;
use App\Transformers\DoctorCaseTransformer;
use App\Transformers\DoctorServiceTransformer;
use App\Transformers\DoctorSurveyTransformer;
use App\Transformers\DocumentTransformer;
use App\Transformers\MessageTransformer;
use App\Transformers\ScenarioTransformer;
use App\User;
use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Cmgmyr\Messenger\Models\Thread;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CasesController extends ApiController
{
    /**
     * Maybe onetime it would be helpful for optimization, but for now I need a lot of queries
     * Load all data that needed by director for the case editing
     * (Will return big json data)
     * @param $id - accident id
     * @return \Dingo\Api\Http\Response
     */
    public function show($id)
    {
        $accident = Accident::findOrFail($id);
        return $this->response->item($accident, new DirectorCaseTransformer());
    }

    public function index(Request $request, AccidentService $service)
    {
        $rows = $request->get('rows', 10);
        $accidents = $service->getCasesQuery($request->all())
            ->paginate($rows, $columns = ['*'], $pageName = 'page', $request->get('page', null)+1);

        return $this->response->paginator($accidents, new CaseAccidentTransformer());
    }

    public function getDoctorCase($id)
    {
        $accident = Accident::findOrFail($id);
        if(!$accident->caseable) {
            $doctorAccident = DoctorAccident::create();
            $accident->caseable_id = $doctorAccident->id;
            $accident->caseable_type = DoctorAccident::class;
            $accident->save();
        }
        return $this->response->item($accident->caseable, new DoctorCaseTransformer());
    }

    public function getHospitalCase($id)
    {
        /*$accident = Accident::findOrCreate($id);
        return $this->response->item($accident->hospitalCase, new HospitalCaseTransformer());*/
        $this->response->errorMethodNotAllowed('Not implemented, yet');
    }

    public function getDiagnostics($id, RoleService $roleService)
    {
        $accident = Accident::findOrFail($id);
        $accidentDiagnostics = $accident->diagnostics;
        if ($accident->caseable) {
            $accidentDiagnostics = $accidentDiagnostics->merge($accident->caseable->diagnostics);
        }
        $accidentDiagnostics->each(function (Diagnostic $diagnostic) use ($roleService) {
            if ($diagnostic->created_by && $roleService->hasRole($diagnostic->creator, 'doctor')) {
                $diagnostic->markAsDoctor();
            }
        });
        return $this->response->collection($accidentDiagnostics, new DiagnosticTransformer());
    }

    public function getServices($id, RoleService $roleService)
    {
        $accident = Accident::findOrFail($id);
        $accidentServices = $accident->services;
        if ($accident->caseable) {
            $accidentServices = $accidentServices->merge($accident->caseable->services);
        }
        $accidentServices->each(function (DoctorService $doctorService) use ($roleService) {
            if ($doctorService->created_by && $roleService->hasRole($doctorService->creator, 'doctor')) {
                $doctorService->markAsDoctor();
            }
        });
        return $this->response->collection($accidentServices, new DoctorServiceTransformer());
    }

    public function getSurveys($id, RoleService $roleService)
    {
        $accident = Accident::findOrFail($id);
        $accidentSurveys = $accident->surveys;
        if ($accident->caseable) {
            $accidentSurveys = $accidentSurveys->merge($accident->caseable->surveys);
        }
        $accidentSurveys->each(function (DoctorSurvey $doctorSurvey) use ($roleService) {
            if ($doctorSurvey->created_by && $roleService->hasRole($doctorSurvey->creator, 'doctor')) {
                $doctorSurvey->markAsDoctor();
            }
        });
        return $this->response->collection($accidentSurveys, new DoctorSurveyTransformer());
    }

    public function getCheckpoints($id)
    {
        $accident = Accident::findOrFail($id);
        return $this->response->collection($accident->checkpoints, new AccidentCheckpointTransformer());
    }

    public function documents($id, DocumentService $documentService)
    {
        $accident = Accident::findOrFail($id);
        return $this->response->collection($documentService->getDocuments($this->user(), $accident, 'accident'), new DocumentTransformer());
    }

    public function createDocuments($id, Request $request, DocumentService $documentService)
    {
        $accident = Accident::findOrFail($id);

        $created = collect([]);
        foreach ($request->allFiles() as $files) {
            $document = $documentService->createDocumentsFromFiles($files, $this->user());
            foreach ($document as $doc) {
                $accident->documents()->attach($doc);
                if ($accident->patient) {
                    $accident->patient->documents()->attach($doc);
                }
                $created->push($doc);
            }
        }

        return $this->response->collection($created, new DocumentTransformer());
    }

    /**
     * New case accident
     * @param Request $request
     * @param ReferralNumberService $referralNumberService
     * @param AccidentStatusesService $statusesService
     * @return \Dingo\Api\Http\Response
     */
    public function store(Request $request, ReferralNumberService $referralNumberService, AccidentStatusesService $statusesService)
    {
        $accidentData = $request->json('accident', []);
        if (!isset($accidentData['handling_time']) || !$accidentData['handling_time']) {
            $accidentData['handling_time'] = NULL;
        }
        $accidentData = array_merge(['contacts' => '', 'symptoms' => ''], $accidentData);
        $accident = Accident::create($accidentData);

        $doctorAccidentData = $request->json('doctorAccident', []);
        if (!isset($doctorAccidentData['visit_time']) || !$doctorAccidentData['visit_time']) {
            $doctorAccidentData['visit_time'] = NULL;
        }
        $doctorAccidentData = array_merge(['recommendation' => '', 'investigation' => ''], $doctorAccidentData);
        $doctorAccident = DoctorAccident::create($doctorAccidentData);

        $patientData = $request->json('patient', []);
        if (!isset($patientData['birthday']) || !$patientData['birthday']) {
            $patientData['birthday'] = null;
        }

        $patient = null;
        if (!isset($patientData['id']) || !$patientData['id']) {
            if (isset($patientData['name']) && $patientData['name']) {
                $patient = Patient::create($patientData);
            }
        } else {
            $patient = Patient::findOrFail($patientData['id']);
        }
        $accident->patient_id = $patient && $patient->id ? $patient->id : 0;
        $accident->caseable_id = $doctorAccident->id;
        $accident->caseable_type = DoctorAccident::class;
        $accident->created_by = $this->user()->id;
        if (empty($accident->ref_num)) {
            $accident->ref_num = $referralNumberService->generate($accident);
        }
        $accident->save();

        $statusesService->set($accident, AccidentStatus::firstOrCreate([
            'title' => AccidentStatusesService::STATUS_NEW,
            'type' => AccidentStatusesService::TYPE_ACCIDENT,
        ]), 'Created by director');

        $accident->diagnostics()->attach($request->json('diagnostics', []));
        $accident->services()->attach($request->json('services', []));
        $accident->surveys()->attach($request->json('surveys', []));
        $accident->documents()->attach($request->json('documents', []));
        $accident->checkpoints()->attach($request->json('checkpoints', []));

        event(new DoctorAccidentUpdatedEvent(null, $doctorAccident, 'Created by director'));
        $transformer = new DirectorCaseTransformer();
        return $this->response->created($accident->id, $transformer->transform($accident));
    }

    /**
     * @param $id
     * @param Request $request
     */
    public function update($id, Request $request)
    {
        $accident = Accident::findOrFail($id);

        // if it needed then status for this accident would be reset by the administrator
        $status = $accident->accidentStatus;

        if (
            $status
            && $status->title == AccidentStatusesService::STATUS_CLOSED
            && $status->type == AccidentStatusesService::TYPE_ACCIDENT) {
                $this->response->errorForbidden('Already closed');
        }

        $requestedAccident = $request->json('accident', false);

        if (!$requestedAccident['id']) {
            \Log::error('Undefined request accident', [
                'accidentId' => $id,
                'requestedAccident' => $requestedAccident
            ]);
            $this->response->errorBadRequest('Accident data should be provided in the request data');
        }

        if (!$requestedAccident['handling_time']) {
            \Log::error('Undefined handling time', [
                'accidentId' => $id,
                'requestedAccident' => $requestedAccident
            ]);
            $this->response->errorBadRequest('Handling time should be provided');
        }

        if ($accident->id != $requestedAccident['id']) {
            \Log::error('Incorrect requested accident', [
                'accidentId' => $id,
                'requestedAccident' => $requestedAccident
            ]);
            $this->response->errorBadRequest('Requested accident did not match to updated one');
        }

        $patientData = $request->json('patient', []);
        if (!$patientData['birthday']) {
            $patientData['birthday'] = null;
        }

        $patient = null;
        if (!$patientData['id']) {
            if ($patientData['name']) {
                $patient = Patient::create($patientData);
            }
        } else {
            $patient = Patient::findOrFail($patientData['id']);
        }

        $requestedAccident['patient_id'] = $patient && $patient->id ? $patient->id : 0;
        $accident = $this->setData($accident, $requestedAccident);
        $accident->save();

        $doctorAccidentData = $request->json('doctorAccident', false);
        if ($doctorAccidentData) {
            if (!$accident->caseable) {
                $doctorAccident = DoctorAccident::create(
                    array_merge(['recommendation' => '', 'investigation' => ''],
                        $request->json('doctorAccident', []))
                );
                $accident->caseable_id = $doctorAccident->id;
                $accident->caseable_type = DoctorAccident::class;
                $accident->save();

                event(new DoctorAccidentUpdatedEvent(null, $doctorAccident, 'Created by director'));
            } else {
                $before = clone $accident->caseable;
                $doctorAccident = $this->setData($accident->caseable, $doctorAccidentData);
                $doctorAccident->save();

                event(new DoctorAccidentUpdatedEvent($before, $doctorAccident, 'Updated by director'));
            }
        }

        $discountData = $request->json('discount', false);
        if ($discountData) {
            $accident->discount_value = floatval($discountData['value']);
            $discount = Discount::find($discountData['type']['id']);
            $accident->discount_id = $discount->id ?: 1;
            $accident->save();
        }

        // Services ==========================
        $services = $request->json('services', []);
        $docServices = [];
        foreach ($accident->caseable->services() as $service) {
            if ($service && in_array($service->id, $services) ) {
                $docServices[] = $services->id;
            }
        }
        $accidentServices = array_diff($services, $docServices);
        $accident->caseable->services()->detach();
        $accident->caseable->services()->attach($docServices);
        $accident->services()->detach();
        $accident->services()->attach($accidentServices);

        // Surveys ==========================
        $surveys = $request->json('surveys', []);
        $docSurveys = [];
        foreach ($accident->caseable->surveys() as $survey) {
            if ($survey && in_array($survey->id, $surveys) ) {
                $docSurveys[] = $surveys->id;
            }
        }
        $accidentSurveys = array_diff($surveys, $docSurveys);
        $accident->caseable->surveys()->detach();
        $accident->caseable->surveys()->attach($docSurveys);
        $accident->surveys()->detach();
        $accident->surveys()->attach($accidentSurveys);

        // Diagnostics ======================
        $diagnostics = $request->json('diagnostics', []);
        $docDiagnostics = [];
        foreach ($accident->caseable->diagnostics() as $diagnostic) {
            if ($diagnostic && in_array($diagnostic->id, $diagnostics) ) {
                $docDiagnostics[] = $diagnostic->id;
            }
        }
        $accidentDiagnostics = array_diff($diagnostics, $docDiagnostics);
        $accident->caseable->diagnostics()->detach();
        $accident->caseable->diagnostics()->attach($docDiagnostics);
        $accident->diagnostics()->detach();
        $accident->diagnostics()->attach($accidentDiagnostics);

        $accident->documents()->detach();
        $accident->documents()->attach($request->json('documents', []));

        $accident->checkpoints()->detach();
        $accident->checkpoints()->attach($request->json('checkpoints', []));
    }

    private function setData(Model $model, $data)
    {
        foreach ($model->getVisible() as $item) {
            if (isset($data[$item])) {
                if (in_array($item, $model->getDates())) {
                    $model->$item = Carbon::parse($data[$item])->format('Y-m-d H:i:s');
                } else {
                    $model->$item = $data[$item];
                }
            }
        }

        return $model;
    }

    /**
     * Load scenario for the current accident
     * @param int $id
     * @param StoryService $storyService
     * @param AccidentStatusesService $accidentStatusesService
     * @param ScenarioService $scenariosService
     * @return \Dingo\Api\Http\Response
     */
    public function story(int $id, StoryService $storyService, AccidentStatusesService $accidentStatusesService, ScenarioService $scenariosService)
    {
        /** @var Accident $accident */
        $accident = Accident::findOrFail($id);
        $scenarioService = null;
        if ($accident->caseable_type == DoctorAccident::class) {
            $scenarioService = new DoctorScenarioService($accidentStatusesService, $scenariosService);
        }

        if ( !($scenarioService instanceof ScenarioInterface) ) {
            $this->response->errorNotFound('Story has not been found for that accident');
        }

        return $this->response->collection(
            $storyService->init($accident->history, $scenarioService)->getStory(),
            new ScenarioTransformer()
        );
    }

    /**
     * Set status closed
     * @param int $id
     * @param AccidentStatusesService $statusesService
     * @return \Dingo\Api\Http\Response
     */
    public function close(int $id, AccidentStatusesService $statusesService)
    {
        $accident = Accident::findOrFail($id);

        $statusesService->set($accident, AccidentStatus::firstOrCreate([
            'title' => AccidentStatusesService::STATUS_CLOSED,
            'type' => AccidentStatusesService::TYPE_ACCIDENT,
        ]), 'Closed by director');

        return $this->response->noContent();
    }

    /**
     * Delete accident
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function destroy($id)
    {
        $accident = Accident::findOrFail($id);
        $accident->delete();
        return $this->response->noContent();
    }

    public function reportHtml($id, CaseReportService $service)
    {
        $accident = Accident::find($id);
        if (!$accident) {
            abort(404);
        }
        return response()->json(['data' => $service->generate($accident)->toHtml()]);
    }

    public function downloadPdf($id, CaseReportService $service)
    {
        $accident = Accident::find($id);
        if (!$accident) {
            abort(404);
        }
        return response()->download($service->generate($accident)->getPdfPath());
    }

    public function history(int $id, CaseHistoryService $service)
    {
        /** @var Accident $accident */
        $accident = Accident::findOrFail($id);

        return $this->response->collection(
            $service->generate($accident)->getHistory(),
            new AccidentStatusHistoryTransformer()
        );
    }

    public function comments(int $id)
    {
        // I need to be sure that there is such accident
        /** @var Accident $accident */
        $accident = Accident::findOrFail($id);
        $thread = Thread::firstOrCreate(['subject' => 'Accident_'.$accident->id]);
        $userId = \Auth::id();
        // $users = User::whereNotIn('id', $thread->participantsUserIds($userId))->get();
        $thread->markAsRead($userId);

        return $this->response->collection(
            $thread->messages,
            new MessageTransformer()
        );
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Dingo\Api\Http\Response
     * @throws \ErrorException
     */
    public function addComment(Request $request, int $id)
    {
        $accident = Accident::findOrFail($id);
        $thread = Thread::firstOrCreate(['subject' => 'Accident_'.$accident->id]);
        $userId = \Auth::id();

        $message = Message::create([
            'thread_id' => $thread->id,
            'user_id' => $userId,
            'body' => $request->json('text', ''),
        ]);

        // Add Sender to participants
        Participant::firstOrCreate([
            'thread_id' => $thread->id,
            'user_id' => $userId,
            'last_read' => new Carbon(),
        ]);

        $transform = new MessageTransformer();
        return $this->response->created(null, $transform->transform($message));
    }
}
