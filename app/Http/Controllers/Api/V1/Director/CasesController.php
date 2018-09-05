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
use App\DoctorAccident;
use App\DoctorService;
use App\DoctorSurvey;
use App\Events\DoctorAccidentUpdatedEvent;
use App\Events\HospitalAccidentUpdatedEvent;
use App\HospitalAccident;
use App\Http\Controllers\ApiController;
use App\Models\Scenario\ScenarioModel;
use App\Patient;
use App\Services\AccidentStatusesService;
use App\Services\CaseServices\CaseHistoryService;
use App\Services\CaseServices\CaseReportService;
use App\Services\DocumentService;
use App\Services\ReferralNumberService;
use App\Services\RoleService;
use App\Services\Scenario\ScenarioService;
use App\Services\Scenario\StoryService;
use App\Transformers\AccidentCheckpointTransformer;
use App\Transformers\AccidentStatusHistoryTransformer;
use App\Transformers\CaseAccidentTransformer;
use App\Transformers\DiagnosticTransformer;
use App\Transformers\DirectorCaseTransformer;
use App\Transformers\DoctorCaseTransformer;
use App\Transformers\DoctorServiceTransformer;
use App\Transformers\DoctorSurveyTransformer;
use App\Transformers\DocumentTransformer;
use App\Transformers\HospitalCaseTransformer;
use App\Transformers\MessageTransformer;
use App\Transformers\ScenarioTransformer;
use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Cmgmyr\Messenger\Models\Thread;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CasesController extends ApiController
{
    /**
     * Datatable model
     * @return string
     */
    protected function getModelClass()
    {
        return Accident::class;
    }

    /**
     * Datatable transformer
     * @return CaseAccidentTransformer|\League\Fractal\TransformerAbstract
     */
    protected function getDataTransformer()
    {
        return new CaseAccidentTransformer();
    }

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

    public function getDoctorCase($id)
    {
        $accident = Accident::findOrFail($id);
        if (!$accident->caseable) {
            $this->response->errorNotFound('Doctor case not found');
        }
        return $this->response->item($accident->caseable, new DoctorCaseTransformer());
    }

    public function getHospitalCase($id)
    {
        $accident = Accident::findOrFail($id);
        if (!$accident->caseable) {
            $this->response->errorNotFound('Hospital case not found');
        }
        return $this->response->item($accident->caseable, new HospitalCaseTransformer());
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
     * Creating caseable
     * @param Accident $accident
     * @param Request $request
     */
    private function createCaseableFromRequest(Accident $accident, Request $request) {
        $caseable = $accident->caseable_type == DoctorAccident::class
            ? DoctorAccident::create(['recommendation' => '', 'investigation' => ''])
            : HospitalAccident::create();

        $accident->caseable_id = $caseable->id;
        $accident->caseable_type = get_class($caseable);
        $accident->save();
        $accident->refresh();
        $this->updateCaseableData($accident, $request);
    }

    private function updateCaseableData(Accident $accident, Request $request)
    {
        if (!$accident->caseable) {
            $this->createCaseableFromRequest($accident, $request);
        } else {
            $isDoc = true;
            if ($accident->caseable_type == DoctorAccident::class) {
                $caseableAccidentData = $request->json('doctorAccident', []);
            } else {
                $isDoc = false;
                $caseableAccidentData = $request->json('hospitalAccident', []);
            }

            $before = clone $accident->caseable;
            $caseable = $this->setData($accident->caseable, $caseableAccidentData);
            $caseable->save();

            if ($isDoc) {
                event(new DoctorAccidentUpdatedEvent($before, $caseable, 'Updated by the director'));
            } else {
                event(new HospitalAccidentUpdatedEvent($before, $caseable, 'Updated by the director'));
            }
        }
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
        if (!isset($accidentData['handlingTime']) || !$accidentData['handlingTime']) {
            $accidentData['handlingTime'] = NULL;
        }
        $accidentData = array_merge(['contacts' => '', 'symptoms' => ''], $accidentData);
        $accidentData = $this->convertIndexes($accidentData);

        $accident = Accident::create($accidentData);

        $this->createCaseableFromRequest($accident, $request);

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
        $accident->created_by = $this->user()->id;

        if (empty($accident->ref_num)) {
            $accident->ref_num = $referralNumberService->generate($accident);
        }
        $accident->save();

        $statusesService->set($accident, AccidentStatus::firstOrCreate([
            'title' => AccidentStatusesService::STATUS_NEW,
            'type' => AccidentStatusesService::TYPE_ACCIDENT
        ]), 'Created by director');

        $accident->diagnostics()->attach($request->json('diagnostics', []));
        $accident->services()->attach($request->json('services', []));
        $accident->surveys()->attach($request->json('surveys', []));
        $accident->documents()->attach($request->json('documents', []));
        $accident->checkpoints()->attach($request->json('checkpoints', []));

        $transformer = new DirectorCaseTransformer();
        return $this->response->created($accident->id, $transformer->transform($accident));
    }

    /**
     * Updated morphed data (services, diagnostics, surveys)
     * @param Accident $accident
     * @param Request $request
     * @param $morphName
     */
    private function updateAccidentMorph(Accident $accident, Request $request, $morphName)
    {
        $morphData = $request->json($morphName, []);
        $caseableMorph = [];
        if ($accident->caseable()->$morphName()) {
            foreach ($accident->caseable->$morphName() as $morph) {
                if ($morph && in_array($morph->id, $morphData) ) {
                    $caseableMorph[] = $morph->id;
                }
            }
        }
        $accident->caseable->$morphName()->detach();
        $accident->caseable->$morphName()->attach($caseableMorph);
        // $accidentMorphs = array_diff($morphData, $caseableMorph);
        // todo why ? $accident->services()->detach();
        //$accident->services()->attach($accidentMorphs);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function update($id, Request $request)
    {
        /** @var Accident $accident */
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

        if (!$requestedAccident['handlingTime']) {
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

        $requestedAccident['patientId'] = $patient && $patient->id ? $patient->id : 0;
        $accident = $this->setData($accident, $requestedAccident);
        $accident->save();

        $this->updateCaseableData($accident, $request);

        $this->updateAccidentMorph($accident, $request, 'services');
        $this->updateAccidentMorph($accident, $request, 'surveys');
        $this->updateAccidentMorph($accident, $request, 'diagnostics');

        $accident->documents()->detach();
        $accident->documents()->attach($request->json('documents', []));

        $accident->checkpoints()->detach();
        $accident->checkpoints()->attach($request->json('checkpoints', []));

        return $this->response->item($accident, new DirectorCaseTransformer());
    }

    /**
     * Makes indexes snake_case because frontend works with camelCase
     * @param $data
     * @return array
     */
    private function convertIndexes($data)
    {
        $converted = [];
        foreach ($data as $key => $val) {
            $converted[snake_case($key)] = $val;
        }
        return $converted;
    }

    /**
     * Set provided data to the visible properties of the model
     * @param Model $model
     * @param $data
     * @return Model
     */
    private function setData(Model $model, $data)
    {
        $data = $this->convertIndexes($data);
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
        $scenario = null;
        $scenario = new ScenarioModel($accidentStatusesService, $scenariosService->getScenarioByTag($accident->caseable_type));

        return $this->response->collection(
            $storyService->init($accident->history, $scenario)->getStory(),
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
