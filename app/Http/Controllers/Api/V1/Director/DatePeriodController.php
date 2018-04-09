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
use App\Transformers\DatePeriodTransformer;
use Illuminate\Http\Request;

class DatePeriodController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function index(Request $request)
    {
        $first = $request->json('first', false);
        $rows = $request->json('rows', 25);
        if ($first !== false) {
            $rows = $rows ? $rows : 25;
            $page = ($first / $rows) + 1;
        } else {
            $page = $request->json('page', 0);
        }

        $sortField = $request->json('sortField', 'title');
        $sortField = $sortField ?: 'title';
        $datePeriods = DatePeriod::orderBy(
                $sortField,
                $request->json('sortOrder', 1) > 0 ? 'asc' : 'desc'
            )
            ->paginate($rows, ['*'], 'page', $page);
        return $this->response->paginator($datePeriods, new DatePeriodTransformer());
    }

    /**
     * Store a newly created resource in storage.
     * @param DatePeriodRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function store(DatePeriodRequest $request)
    {
        if ($request->json('id', false)) {
            $this->response->errorBadRequest();
        }
        $datePeriod = DatePeriod::create($request->json()->all());
        \Log::info('Period created', [$datePeriod, $this->user()]);
        $transformer = new DatePeriodTransformer();
        return $this->response->created(self::class, $transformer->transform($datePeriod));
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
     * @return \Dingo\Api\Http\Response
     */
    public function update(DatePeriodRequest $request, $id)
    {
        $datePeriod = DatePeriod::findOrFail($id);
        $datePeriod->update($request->json());
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
