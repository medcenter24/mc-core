<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\Accident;
use App\Diagnostic;
use App\DoctorAccident;
use App\DoctorService;
use App\DoctorSurvey;
use App\Events\DoctorAccidentUpdatedEvent;
use App\Events\HospitalAccidentUpdatedEvent;
use App\HospitalAccident;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\CaseRequest;
use App\Models\Scenario\ScenarioModel;
use App\Services\AccidentService;
use App\Services\AccidentStatusesService;
use App\Services\CaseServices\CaseFinanceService;
use App\Services\CaseServices\CaseHistoryService;
use App\Services\CurrencyService;
use App\Services\DocumentService;
use App\Services\Formula\FormulaResultService;
use App\Services\Formula\FormulaViewService;
use App\Services\PatientService;
use App\Services\ReferralNumberService;
use App\Services\RoleService;
use App\Services\Scenario\ScenarioService;
use App\Services\Scenario\StoryService;
use App\Transformers\AccidentCheckpointTransformer;
use App\Transformers\AccidentStatusHistoryTransformer;
use App\Transformers\CaseAccidentTransformer;
use App\Transformers\CaseFinanceTransformer;
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
use Dingo\Api\Http\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

    public function getServices($id, RoleService $roleService, AccidentService $accidentServiceService)
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
                // attach services, surveys and diagnostics
                $this->updateDoctorMorph($accident->caseable, $request, 'services');
                $this->updateDoctorMorph($accident->caseable, $request, 'surveys');
                $this->updateDoctorMorph($accident->caseable, $request, 'diagnostics');
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
     * @param CaseRequest $request
     * @param ReferralNumberService $referralNumberService
     * @param AccidentStatusesService $statusesService
     * @param AccidentService $accidentService
     * @param PatientService $patientService
     * @return \Dingo\Api\Http\Response
     */
    public function store(
        CaseRequest $request,
        ReferralNumberService $referralNumberService,
        AccidentStatusesService $statusesService,
        AccidentService $accidentService,
        PatientService $patientService
    ) {

        $accidentData = $accidentService->getFormattedAccidentData(
            $this->convertIndexes(
                $request->json('accident', [])
            )
        );

        $accident = Accident::create($accidentData);
        $this->createCaseableFromRequest($accident, $request);

        if (!array_key_exists('patientId', $accidentData)) {
            $patient = $patientService->findOrCreate($request->json('patient', []));
            $accident->patient_id = $patient && $patient->id ? $patient->id : 0;
        } else {
            $accident->patient_id = intval($accidentData['patientId']);
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
     * @param $morphName
     */
    private function updateDoctorMorph(DoctorAccident $doctorAccident, Request $request, $morphName)
    {
        $morphData = $request->json($morphName, []);
        $morphs = [];
        if ($doctorAccident->$morphName()) {
            foreach ($doctorAccident->$morphName() as $morph) {
                if ($morph && in_array($morph->id, $morphData) ) {
                    $morphs[] = $morph->id;
                }
            }
        }
        $doctorAccident->$morphName()->detach();
        $doctorAccident->$morphName()->attach($morphs);
    }

    /**
     * @param $id
     * @param CaseRequest $request
     * @param AccidentService $accidentService
     * @param PatientService $patientService
     * @return \Dingo\Api\Http\Response
     */
    public function update(
        $id,
        CaseRequest $request,
        AccidentService $accidentService,
        PatientService $patientService
    ) {
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
            \Log::error('Can not update the case: undefined request accident', [
                'accidentId' => $id,
                'requestedAccident' => $requestedAccident
            ]);
            $this->response->errorBadRequest('Accident data should be provided in the request data');
        }

        if ($accident->id != $requestedAccident['id']) {
            \Log::error('Can not update the case: incorrect requested accident', [
                'accidentId' => $id,
                'requestedAccident' => $requestedAccident
            ]);
            $this->response->errorBadRequest('Requested accident did not match to updated one');
        }

        if (!key_exists('patientId', $requestedAccident)) {
            $patient = $patientService->findOrCreate($request->json('patient', []));
            $newPatientId = $patient && $patient->id ? $patient->id : 0;
            if ($newPatientId) {
                $requestedAccident['patientId'] = $newPatientId;
            } elseif ($accident->patient_id) {
                $requestedAccident['patientId'] = $accident->patient_id;
            }
        } else {
            $requestedAccident['patientId'] = intval($requestedAccident['patientId']);
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
    private function setData(Model $model, $data): Model
    {
        $data = $this->convertIndexes($data);
        foreach ($model->getVisible() as $item) {
            if (array_key_exists($item, $data)) {
                if (in_array($item, $model->getDates(), true)) {
                    $model->$item = $data[$item] ? Carbon::parse($data[$item])->format('Y-m-d H:i:s') : null;
                } else {
                    $model->$item = $data[$item] ?: '';
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
     * @param AccidentStatusesService $statusesService
     * @return \Dingo\Api\Http\Response
     */
    public function close(int $id, AccidentStatusesService $statusesService): Response
    {
        $accident = Accident::findOrFail($id);
        $statusesService->closeAccident($accident, 'Closed by director');
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

    /**
     * @param Request $request
     * @param int $id
     * @param CaseFinanceService $financeService
     * @param FormulaResultService $formulaResultService
     * @param FormulaViewService $formulaViewService
     * @param CurrencyService $currencyService
     * @return Response
     * @throws \App\Exceptions\InconsistentDataException
     * @throws \App\Models\Formula\Exception\FormulaException
     * @throws \Throwable
     */
    public function finance(
        Request $request,
        int $id,
        CaseFinanceService $financeService,
        FormulaResultService $formulaResultService,
        FormulaViewService $formulaViewService,
        CurrencyService $currencyService
    ): Response
    {
        /** @var Accident $accident */
        $accident = Accident::findOrFail($id);
        $financeDataCollection = collect([]);

        $types = $request->json('types', ['income', 'assistant', 'caseable']);
        $formula = null;
        foreach ($types as $type) {
            switch ($type) {
                case 'income':
                    $formula = $financeService->newFormula();
                    if ($accident->getIncomePayment && $accident->getIncomePayment->fixed) {
                        $formula->addFloat($accident->getIncomePayment->value);
                    } else {
                        // I need to sub 2 different results instead of sub formula builders
                        // to not get 1. big formula 2. data inconsistencies
                        $formula
                            ->subFloat($formulaResultService->calculate($financeService->getFromAssistantFormula($accident)))
                            ->subFloat($formulaResultService->calculate($financeService->getToCaseableFormula($accident)));
                    }
                    break;
                case 'assistant':
                    $formula = $financeService->getFromAssistantFormula($accident);
                    break;
                case 'caseable':
                    // answer: I need this formula just to use it for consistency
                    // so in the formula I just need to set price from the invoice
                    $formula = $financeService->getToCaseableFormula($accident);
                    break;
                default:
                    $this->response->error('undefined finance type', 500);
            }

            $typeResult = collect([
                'type' => $type,
                'loading' => false,
                'value' => $formulaResultService->calculate($formula),
                'currency' => $currencyService->getDefaultCurrency(),
                'formula' => $formulaViewService->render($formula),
            ]);
            $financeDataCollection->push($typeResult);
        }

        $obj = new \stdClass();
        $obj->collection = $financeDataCollection;
        return $this->response->item($obj, new CaseFinanceTransformer());
    }
}
