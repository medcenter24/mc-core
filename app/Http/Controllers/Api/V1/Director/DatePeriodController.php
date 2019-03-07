<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\DatePeriod;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\DatePeriodRequest;
use App\Services\DatePeriod\DatePeriodService;
use App\Transformers\DatePeriodTransformer;
use League\Fractal\TransformerAbstract;

class DatePeriodController extends ApiController
{
    protected function getDataTransformer(): TransformerAbstract
    {
        return new DatePeriodTransformer();
    }

    protected function getModelClass(): string
    {
        return DatePeriod::class;
    }

    /**
     * Store a newly created resource in storage.
     * @param DatePeriodRequest $request
     * @param DatePeriodService $service
     * @return \Dingo\Api\Http\Response
     */
    public function store(DatePeriodRequest $request, DatePeriodService $service)
    {
        if ($request->json('id', false)) {
            $this->response->errorBadRequest();
        }
        $datePeriod = $service->save($request->json()->all());
        $transformer = new DatePeriodTransformer();
        return $this->response->created(url('api/director/period/'.$datePeriod->id), $transformer->transform($datePeriod));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->response->item(DatePeriod::findOrFail($id), new DatePeriodTransformer());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param DatePeriodRequest $request
     * @param $id
     * @param DatePeriodService $service
     * @return \Dingo\Api\Http\Response
     */
    public function update(DatePeriodRequest $request, $id, DatePeriodService $service)
    {
        $datePeriod = $service->save($request->json()->all());
        return $this->response->item($datePeriod, new DatePeriodTransformer());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     * @throws \Exception
     */
    public function destroy($id)
    {
        $datePeriod = DatePeriod::findOrFail($id);
        $datePeriod->delete();
        return $this->response->noContent();
    }
}