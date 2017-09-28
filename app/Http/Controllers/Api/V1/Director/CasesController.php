<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\Accident;
use App\AccidentStatus;
use App\AccidentStatusHistory;
use App\Discount;
use App\DoctorAccident;
use App\Document;
use App\Events\AccidentUpdated;
use App\Events\DoctorAccidentUpdatedEvent;
use App\Http\Controllers\ApiController;
use App\Patient;
use App\Services\AccidentService;
use App\Services\AccidentStatusesService;
use App\Services\DocumentService;
use App\Services\ReferralNumberService;
use App\Services\Scenario\DoctorScenarioService;
use App\Services\Scenario\StoryService;
use App\Services\ScenarioInterface;
use App\Transformers\AccidentCheckpointTransformer;
use App\Transformers\CaseAccidentTransformer;
use App\Transformers\DiagnosticTransformer;
use App\Transformers\DirectorCaseTransformer;
use App\Transformers\DoctorCaseTransformer;
use App\Transformers\DoctorServiceTransformer;
use App\Transformers\DocumentTransformer;
use App\Transformers\ScenarioTransformer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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

    public function getDiagnostics($id)
    {
        $accident = Accident::findOrFail($id);
        $accidentDiagnostics = $accident->diagnostics;
        if ($accident->caseable) {
            $accidentDiagnostics = $accidentDiagnostics->merge($accident->caseable->diagnostics);
        }
        return $this->response->collection($accidentDiagnostics, new DiagnosticTransformer());
    }

    public function getServices($id)
    {
        $accident = Accident::findOrFail($id);
        $accidentServices = $accident->services;
        if ($accident->caseable) {
            $accidentServices = $accidentServices->merge($accident->caseable->services);
        }
        return $this->response->collection($accidentServices, new DoctorServiceTransformer());
    }

    public function getCheckpoints($id)
    {
        $accident = Accident::findOrFail($id);
        return $this->response->collection($accident->checkpoints, new AccidentCheckpointTransformer());
    }

    public function documents($id, DocumentService $documentService)
    {
        $accident = Accident::findOrFail($id);
        return $this->response->collection($documentService->getDocuments($this->user(), $accident), new DocumentTransformer());
    }

    public function createDocuments($id, Request $request, DocumentService $documentService)
    {
        $accident = Accident::findOrFail($id);
        $documents = new Collection();

        foreach ($request->allFiles() as $files) {
            $document = $documentService->createDocumentsFromFiles($files, $this->user());
            $documents->merge($document);
            if ($accident) {
                $accident->documents()->attach($document);
                $accident->patient->documents()->attach($document);
            }
        }

        return $this->response->collection($documents, new DocumentTransformer());
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
        $accident = Accident::create(
            array_merge(['contacts' => '', 'symptoms' => ''], $request->json('accident', []))
        );
        $doctorAccident = DoctorAccident::create(
            array_merge(['recommendation' => '', 'investigation' => ''], $request->json('doctorAccident', []))
        );
        $accident->caseable_id = $doctorAccident->id;
        $accident->caseable_type = DoctorAccident::class;
        $accident->created_by = $this->user()->id;
        $accident->save();

        $patientData = $request->json('patient', []);
        if (isset($patientData['id']) && !$patientData['id']) {
            unset($patientData['id']);
        }
        if (isset($patientData['birthday']) && empty($patientData['birthday'])) {
            unset($patientData['birthday']);
        }
        $patient = Patient::firstOrCreate($patientData);
        $accident->patient_id = $patient->id;
        $accident->ref_num = $referralNumberService->generate($accident);
        $accident->save();

        $statusesService->set($accident, AccidentStatus::firstOrCreate([
            'title' => AccidentStatusesService::STATUS_NEW,
            'type' => AccidentStatusesService::TYPE_ACCIDENT,
        ]), 'Created by director');

        $accident->diagnostics()->attach($request->json('diagnostics', []));
        $accident->services()->attach($request->json('services', []));
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
            $status->title == AccidentStatusesService::STATUS_CLOSED
            && $status->type == AccidentStatusesService::TYPE_ACCIDENT) {
            $this->response->errorForbidden('Already closed');
        }

        $requestedAccident = $request->json('accident', false);

        if (!$requestedAccident['id']) {
            \Log::error('Undefined request accident', [
                'accidentId' => $id,
                'requestedAccident' => $requestedAccident
            ]);
            $this->response->errorBadRequest(trans('Accident data should be provided in the request data'));
        }

        if ($accident->id != $requestedAccident['id']) {
            \Log::error('Incorrect requested accident', [
                'accidentId' => $id,
                'requestedAccident' => $requestedAccident
            ]);
            $this->response->errorBadRequest(trans('Requested accident did not match to updated one'));
        }

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

        $patientData = $request->json('patient', false);
        if ($patientData) {
            if (!$accident->patient) {
                $patient = Patient::firstOrCreate($patientData);
                $accident->patient_id = $patient->id;
            } else {
                if (isset($patientData['birthday']) && empty($patientData['birthday'])) {
                    DB::update('UPDATE patients SET birthday=NULL WHERE id='.$accident->patient->id);
                    unset($patientData['birthday']);
                }
                $patient = $this->setData($accident->patient, $patientData);
                $patient->save();
            }
        }

        $discountData = $request->json('discount', false);
        if ($discountData) {
            $accident->discount_value = floatval($discountData['value']);
            $discount = Discount::find($discountData['type']['id']);
            $accident->discount_id = $discount->id ?: 1;
            $accident->save();
        }

        $accident->services()->detach();
        $accident->services()->attach($request->json('services', []));

        $accident->diagnostics()->detach();
        $accident->diagnostics()->attach($request->json('diagnostics', []));

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
     * @return \Dingo\Api\Http\Response
     */
    public function story(int $id, StoryService $storyService)
    {
        /** @var Accident $accident */
        $accident = Accident::findOrFail($id);
        $scenarioService = null;
        if ($accident->caseable_type == DoctorAccident::class) {
            $scenarioService = new DoctorScenarioService();
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
}
