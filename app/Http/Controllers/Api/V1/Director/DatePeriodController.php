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

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director;

use medcenter24\mcCore\App\Entity\DatePeriod;
use medcenter24\mcCore\App\Http\Controllers\Api\ModelApiController;
use medcenter24\mcCore\App\Http\Requests\Api\DatePeriodRequest;
use medcenter24\mcCore\App\Services\DatePeriod\DatePeriodService;
use medcenter24\mcCore\App\Transformers\DatePeriodTransformer;
use Dingo\Api\Http\Response;
use League\Fractal\TransformerAbstract;

class DatePeriodController extends ModelApiController
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
     * @return Response
     */
    public function store(DatePeriodRequest $request, DatePeriodService $service): Response
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
     * @return Response
     */
    public function show($id): Response
    {
        return $this->response->item(DatePeriod::findOrFail($id), new DatePeriodTransformer());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param DatePeriodRequest $request
     * @param $id
     * @param DatePeriodService $service
     * @return Response
     */
    public function update(DatePeriodRequest $request, $id, DatePeriodService $service): Response
    {
        $datePeriod = $service->save($request->json()->all());
        return $this->response->item($datePeriod, new DatePeriodTransformer());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return Response
     * @throws \Exception
     */
    public function destroy($id): Response
    {
        $datePeriod = DatePeriod::findOrFail($id);
        $datePeriod->delete();
        return $this->response->noContent();
    }
}
