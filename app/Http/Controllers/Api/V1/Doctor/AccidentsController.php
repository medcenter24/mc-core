<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Doctor;

use App\Accident;
use App\DoctorAccident;
use App\Http\Controllers\ApiController;
use App\Transformers\AccidentTransformer;
use App\Transformers\AccidentTypeTransformer;
use App\Transformers\CaseAccidentTransformer;
use App\Transformers\DiagnosticTransformer;
use App\Transformers\DoctorAccidentStatusTransformer;
use App\Transformers\DoctorServiceTransformer;
use App\Transformers\PatientTransformer;
use Illuminate\Http\Request;

class AccidentsController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $rows = $request->get('per_page', 10);
        $accidents = Accident::orderBy('created_at', 'desc')
            ->paginate($rows, $columns = ['*'], $pageName = 'page', $request->get('page', null));
        return $this->response->paginator($accidents, new CaseAccidentTransformer());
    }

    /**
     * Closed or accident which were sent which can't be changed
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $accident = Accident::find($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        return $this->response->item($accident, new AccidentTransformer());
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

        return $this->response->collection($accident->services, new DoctorServiceTransformer());
    }

    public function type($id)
    {
        $accident = Accident::find($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        return $this->response->item($accident->type, new AccidentTypeTransformer());
    }

    public function surveys($id)
    {
        $accident = Accident::find($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        return $this->response->collection($accident->diagnostics, new DiagnosticTransformer());
    }

    /**
     * Edit form for the accident which should be edited by the doctor
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Reject the accident
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
