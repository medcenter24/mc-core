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

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use medcenter24\mcCore\App\Accident;
use medcenter24\mcCore\App\Diagnostic;
use medcenter24\mcCore\App\DoctorAccident;
use medcenter24\mcCore\App\DoctorService;
use medcenter24\mcCore\App\DoctorSurvey;
use medcenter24\mcCore\App\Events\DoctorAccidentUpdatedEvent;
use medcenter24\mcCore\App\Events\HospitalAccidentUpdatedEvent;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\HospitalAccident;
use medcenter24\mcCore\App\Http\Controllers\ApiController;
use medcenter24\mcCore\App\Http\Requests\Api\CaseRequest;
use medcenter24\mcCore\App\Models\Scenario\ScenarioModel;
use medcenter24\mcCore\App\Services\AccidentService;
use medcenter24\mcCore\App\Services\AccidentStatusesService;
use medcenter24\mcCore\App\Services\CaseServices\CaseHistoryService;
use medcenter24\mcCore\App\Services\DocumentService;
use medcenter24\mcCore\App\Services\PatientService;
use medcenter24\mcCore\App\Services\ReferralNumberService;
use medcenter24\mcCore\App\Services\RoleService;
use medcenter24\mcCore\App\Services\Scenario\ScenarioService;
use medcenter24\mcCore\App\Services\Scenario\StoryService;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;
use medcenter24\mcCore\App\Transformers\AccidentCheckpointTransformer;
use medcenter24\mcCore\App\Transformers\AccidentStatusHistoryTransformer;
use medcenter24\mcCore\App\Transformers\CaseAccidentTransformer;
use medcenter24\mcCore\App\Transformers\DiagnosticTransformer;
use medcenter24\mcCore\App\Transformers\DirectorCaseTransformer;
use medcenter24\mcCore\App\Transformers\DoctorCaseTransformer;
use medcenter24\mcCore\App\Transformers\DoctorServiceTransformer;
use medcenter24\mcCore\App\Transformers\DoctorSurveyTransformer;
use medcenter24\mcCore\App\Transformers\DocumentTransformer;
use medcenter24\mcCore\App\Transformers\HospitalCaseTransformer;
use medcenter24\mcCore\App\Transformers\MessageTransformer;
use medcenter24\mcCore\App\Transformers\ScenarioTransformer;
use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Cmgmyr\Messenger\Models\Thread;
use Dingo\Api\Http\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use League\Fractal\TransformerAbstract;


class CasesController extends ApiController
{
    use ServiceLocatorTrait;

    /**
     * Datatable model
     * @return string
     */
    protected function getModelClass(): string
    {
        return Accident::class;
    }

    /**
     * Datatable transformer
     * @return CaseAccidentTransformer|TransformerAbstract
     */
    protected function getDataTransformer(): TransformerAbstract
    {
        return new CaseAccidentTransformer();
    }

    /**
     * @param $eloquent
     * @param Request|null $request
     * @return Builder
     */
    protected function applyCondition($eloquent, Request $request = null): Builder
    {
        if ($request) {
            $filters = $request->json('filters', false);
            if (is_array($filters) && array_key_exists('find', $filters) && !empty($filters['find'])) {
                $eloquent->where('ref_num', $filters['find']);
            }
        }
        return $eloquent;
    }

    /**
     * Maybe onetime it would be helpful for optimization, but for now I need a lot of queries
     * Load all data that needed by director for the case editing
     * (Will return big json data)
     * @param $id - accident id
     * @return Response
     */
    public function show($id): Response
    {
        $accident = Accident::findOrFail($id);
        return $this->response->item($accident, new DirectorCaseTransformer());
    }

    public function getDoctorCase($id): Response
    {
        $accident = Accident::findOrFail($id);
        if (!$accident->caseable) {
            $this->response->errorNotFound('Doctor case not found');
        }
        return $this->response->item($accident->caseable, new DoctorCaseTransformer());
    }

    public function getHospitalCase($id): Response
    {
        $accident = Accident::findOrFail($id);
        if (!$accident->caseable) {
            $this->response->errorNotFound('Hospital case not found');
        }
        return $this->response->item($accident->caseable, new HospitalCaseTransformer());
    }

    public function getDiagnostics($id, RoleService $roleService): Response
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

    public function getServices($id, RoleService $roleService, AccidentService $accidentServiceService): Response
    {
        $accident = Accident::findOrFail($id);
        $accidentServices = $accidentServiceService->getAccidentServices($accident);
        $accidentServices->each(function (DoctorService $doctorService) use ($roleService) {
            if ($doctorService->created_by && $roleService->hasRole($doctorService->creator, 'doctor')) {
                $doctorService->markAsDoctor();
            }
        });
        return $this->response->collection($accidentServices, new DoctorServiceTransformer());
    }

    public function getSurveys($id, RoleService $roleService): Response
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

    public function getCheckpoints($id): Response
    {
        $accident = Accident::findOrFail($id);
        return $this->response->collection($accident->checkpoints, new AccidentCheckpointTransformer());
    }

    public function documents($id): Response
    {
        $accident = Accident::findOrFail($id);
        $documents = $this->getServiceLocator()->get(DocumentService::class)->getDocuments($this->user(), $accident, 'accident');
        return $this->response->collection($documents, new DocumentTransformer());
    }

    public function createDocuments($id, Request $request, DocumentService $documentService): Response
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
    private function createCaseableFromRequest(Accident $accident, Request $request): void
    {
        $caseable = $accident->caseable_type === DoctorAccident::class
            ? DoctorAccident::create(['recommendation' => '', 'investigation' => ''])
            : HospitalAccident::create();

        $accident->caseable_id = $caseable->id;
        $accident->caseable_type = get_class($caseable);
        $accident->save();
        $accident->refresh();
        $this->updateCaseableData($accident, $request);
    }

    /**
     * Updating caseable
     * @param Accident $accident
     * @param Request $request
     */
    private function updateCaseableData(Accident $accident, Request $request): void
    {
        if (!$accident->caseable) {
            $this->createCaseableFromRequest($accident, $request);
        } else {
            if ($accident->isDoctorCaseable()) {
                $caseableAccidentData = $request->json('doctorAccident', []);
                // attach services, surveys and diagnostics
                $this->updateDoctorMorph($accident->caseable, $request, 'services');
                $this->updateDoctorMorph($accident->caseable, $request, 'surveys');
                $this->updateDoctorMorph($accident->caseable, $request, 'diagnostics');
            } else {
                $caseableAccidentData = $request->json('hospitalAccident', []);
            }

            $before = clone $accident->caseable;
            $caseable = $this->setData($accident->caseable, $caseableAccidentData);
            $caseable->save();

            if ($accident->isDoctorCaseable()) {
                event(new DoctorAccidentUpdatedEvent($before, $caseable, 'Updated by the director'));
            } else {
                event(new HospitalAccidentUpdatedEvent($before, $caseable, 'Updated by the director'));
            }
        }
    }

    /**
     * New case accident
     * @param CaseRequest $request
     * @param ReferralNumberService $referralNumberService
     * @param AccidentService $accidentService
     * @param PatientService $patientService
     * @return Response
     */
    public function store(
        CaseRequest $request,
        ReferralNumberService $referralNumberService,
        AccidentService $accidentService,
        PatientService $patientService
    ): Response {

        $accidentData = $this->convertIndexes( $request->json('accident', []) );
        /** @var Accident $accident */
        $accident = $accidentService->create($accidentData);
        $this->createCaseableFromRequest($accident, $request);

        if (!array_key_exists('patientId', $accidentData)) {
            // if patient data were provided then try to find or create him
            $patientData = $request->json('patient', []);
            if (count($patientData)) {
                $patient = $patientService->firstOrCreate($patientData);
                $accident->patient_id = $patient->id;
            }
        } else {
            $accident->patient_id = (int) $accidentData['patientId'];
        }

        $accident->created_by = $this->user()->id;

        if (empty($accident->ref_num)) {
            $accident->ref_num = $referralNumberService->generate($accident);
        }
        $accident->save();

        // I can provide list of documents to assign them to the accident directly
        $accident->documents()->detach();
        $accident->documents()->attach($request->json('documents', []));

        $accident->checkpoints()->detach();
        $accident->checkpoints()->attach($request->json('checkpoints', []));

        $transformer = new DirectorCaseTransformer();
        return $this->response->created($accident->id, $transformer->transform($accident));
    }

    /**
     * Updated morphed data (services, diagnostics, surveys)
     * @param DoctorAccident $doctorAccident
     * @param Request $request
     * @param string $morphName
     */
    private function updateDoctorMorph(DoctorAccident $doctorAccident, Request $request, string $morphName): void
    {
        $morphs = $request->json($morphName, []);
        /*
         * I need to go through the new parameters, not the already stored
         * (definitely for the services)
         *
         * not clear why do I need it at all
         * if ($doctorAccident->$morphName()) {
            foreach ($doctorAccident->$morphName() as $morph) {
                if ($morph && in_array($morph->id, $morphData, false) ) {
                    $morphs[] = $morph->id;
                }
            }
        }*/
        $doctorAccident->$morphName()->detach();
        $doctorAccident->$morphName()->attach($morphs);
    }

    /**
     * @param $id
     * @param CaseRequest $request
     * @param AccidentService $accidentService
     * @param PatientService $patientService
     * @return Response
     */
    public function update(
        $id,
        CaseRequest $request,
        AccidentService $accidentService,
        PatientService $patientService
    ): Response {
        /** @var Accident $accident */
        try {
            $accident = Accident::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $this->response->errorForbidden('Accident not found');
        }

        // if it needed then status for this accident would be reset by the administrator
        if ($accidentService->isClosed($accident)) {
            $this->response->errorForbidden('Already closed');
        }

        $requestedAccident = $request->json('accident', false);

        if (!$requestedAccident['id']) {
            Log::error('Can not update the case: undefined request accident', [
                'accidentId' => $id,
                'requestedAccident' => $requestedAccident
            ]);
            $this->response->errorBadRequest('Accident data should be provided in the request data');
        }

        if ($accident->id !== $requestedAccident['id']) {
            Log::error('Can not update the case: incorrect requested accident', [
                'accidentId' => $id,
                'requestedAccident' => $requestedAccident
            ]);
            $this->response->errorBadRequest('Requested accident did not match to updated one');
        }

        if (!array_key_exists('patientId', $requestedAccident)) {
            $patient = $patientService->firstOrCreate($request->json('patient', []));
            $newPatientId = $patient && $patient->id ? $patient->id : 0;
            if ($newPatientId) {
                $requestedAccident['patientId'] = $newPatientId;
            } elseif ($accident->patient_id) {
                $requestedAccident['patientId'] = $accident->patient_id;
            }
        } else {
            $requestedAccident['patientId'] = (int) $requestedAccident['patientId'];
        }
        // I don't need to update all data only to update provided, so I don't need use `getFormattedAccidentData`
        $accident = $this->setData($accident, $requestedAccident);
        $accident->save();

        $this->updateCaseableData($accident, $request);

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
    private function convertIndexes(array $data): array
    {
        $converted = [];
        foreach ($data as $key => $val) {
            $converted[Str::snake($key)] = $val;
        }
        return $converted;
    }

    /**
     * Set provided data to the visible properties of the model
     * @param Model $model
     * @param $data
     * @return Model
     */
    private function setData(Model $model, array $data): Model
    {
        $data = $this->convertIndexes($data);
        foreach ($model->getVisible() as $item) {
            if (array_key_exists($item, $data)) {
                if (in_array($item, $model->getDates(), true)) {
                    $model->$item = $data[$item] ? Carbon::parse($data[$item])->format('Y-m-d H:i:s') : null;
                } else {
                    if (mb_strstr($item, 'id')) {
                        $model->$item = (int)$data[$item];
                    } else {
                        $model->$item = $data[$item] ?: '';
                    }
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
    public function story(
        int $id,
        StoryService $storyService,
        AccidentStatusesService $accidentStatusesService,
        ScenarioService $scenariosService
    ) : Response
    {
        /** @var Accident $accident */
        $accident = Accident::findOrFail($id);
        $scenario = new ScenarioModel($accidentStatusesService, $scenariosService->getScenarioByTag($accident->caseable_type));

        return $this->response->collection(
            $storyService->init($accident->history, $scenario)->getStory(),
            new ScenarioTransformer()
        );
    }

    /**
     * Set status closed
     * @param int $id
     * @param AccidentService $accidentService
     * @return Response
     * @throws InconsistentDataException
     */
    public function close(int $id, AccidentService $accidentService): Response
    {
        $accident = Accident::findOrFail($id);
        $accidentService->closeAccident($accident, 'Closed by director');
        return $this->response->noContent();
    }

    /**
     * Delete accident
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function destroy($id): Response
    {
        $accident = Accident::findOrFail($id);
        $accident->delete();
        return $this->response->noContent();
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

    public function comments(int $id): Response
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
    public function addComment(Request $request, int $id): Response
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
