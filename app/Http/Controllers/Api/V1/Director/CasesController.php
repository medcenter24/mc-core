<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\Accident;
use App\Discount;
use App\Document;
use App\Http\Controllers\ApiController;
use App\Services\UploaderService;
use App\Transformers\AccidentTransformer;
use App\Transformers\CaseAccidentTransformer;
use App\Transformers\DiagnosticTransformer;
use App\Transformers\DirectorCaseTransformer;
use App\Transformers\DoctorCaseTransformer;
use App\Transformers\DoctorServiceTransformer;
use App\Transformers\DocumentTransformer;
use App\Transformers\UploadedFileTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CasesController extends ApiController
{
    /**
     * Maybe sometime it would be helpful for optimization, but for now I need a lot of queries
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

    public function index(Request $request)
    {
        $rows = $request->get('rows', 10);
        $accidents = Accident::orderBy('created_at', 'desc')->paginate($rows, $columns = ['*'], $pageName = 'page', $request->get('page', null)+1);

        return $this->response->paginator($accidents, new CaseAccidentTransformer());
    }

    public function getDoctorCase($id)
    {
        $accident = Accident::findOrFail($id);
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
        $doctorAccidentDiagnostics = $accident->caseable->diagnostics;
        return $this->response->collection($accidentDiagnostics->merge($doctorAccidentDiagnostics), new DiagnosticTransformer());
    }

    public function getServices($id)
    {
        $accident = Accident::findOrFail($id);
        $accidentServices = $accident->services;
        $doctorAccidentServices = $accident->caseable->services;
        return $this->response->collection($accidentServices->merge($doctorAccidentServices), new DoctorServiceTransformer());
    }

    public function documents($id)
    {
        // todo recognize why merge doesn't work
        $documents = $this->user()->documents;
        $accident = Accident::find($id);
        if ($accident) {
            $accidentDocs = $accident->documents;
            $documents->merge($accidentDocs);
            $documents->merge($accident->caseable->documents);
        }

        return $this->response->collection($documents, new DocumentTransformer());
    }

    public function createDocuments($id, Request $request)
    {
        $accident = Accident::find($id);
        $documents = new Collection();

        foreach ($request->allFiles() as $files) {
            foreach ($files as $file) {
                /** @var Document $document */
                $document = Document::create([
                    'title' => $file->getClientOriginalName()
                ]);
                $document->addMedia($file)->toMediaCollection();
                $documents->push($document);

                if ($accident) {
                    $accident->documents()->attach($document);
                    $accident->patient->documents()->attach($document);
                } else {
                    $this->user()->documents()->attach($document);
                }
            }
        }

        return $this->response->collection($documents, new DocumentTransformer());
    }

    public function create()
    {

    }

    public function update($id, Request $request)
    {
        $accident = Accident::findOrFail($id);

        // todo check accident status if it was sent and marked as sent then decline to change it
        // if it needed then status for this accident would be reset by the administrator

        $requestedAccident = $request->get('accident', false);

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
        if (!$request->has('doctorAccident')) {
            $doctorAccident = $this->setData($accident->caseable, $request->get('doctorAccident'));
            $doctorAccident->save();
        }
        if (!$request->has('patient')) {
            $patient = $this->setData($accident->patient, $request->get('patient'));
            $patient->save();
        }
        if ($request->has('discount')) {
            $discountData = $request->get('discount');
            $accident->discount_value = floatval($discountData['value']);
            $discount = Discount::find($discountData['type']['id']);
            $accident->discount_id = $discount->id ?: 1;
            $accident->save();
        }

        // todo
        // doctor
        // city
        // services
        // diagnostics
        // files
    }

    private function setData(Model $model, $data)
    {
        foreach ($model->getVisible() as $item) {
            if (isset($data[$item])) {
                $model->$item = $data[$item];
            }
        }
        return $model;
    }
}
