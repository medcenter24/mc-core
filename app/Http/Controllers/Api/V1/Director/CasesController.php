<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\Accident;
use App\DoctorAccident;
use App\Http\Controllers\ApiController;
use App\Transformers\CasesTransformer;
use App\Transformers\DiagnosticTransformer;
use App\Transformers\DoctorCaseTransformer;
use App\Transformers\DoctorServiceTransformer;
use App\Transformers\ModelTransformer;
use Illuminate\Http\Request;

class CasesController extends ApiController
{
    public function index(Request $request)
    {
        $rows = $request->get('rows', 10);
        $accidents = Accident::orderBy('created_at', 'desc')->paginate($rows, $columns = ['*'], $pageName = 'page', $request->get('page', null)+1);

        return $this->response->paginator($accidents, new CasesTransformer);
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
}
