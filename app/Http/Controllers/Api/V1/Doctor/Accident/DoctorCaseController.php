<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2020 (original work) MedCenter24.com;
 */

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor\Accident;

use Dingo\Api\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Http\Controllers\Api\ApiController;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\AccidentStatusService;
use medcenter24\mcCore\App\Transformers\DoctorAccidentTransformer;
use medcenter24\mcCore\App\Transformers\DoctorCaseAccidentTransformer;

class DoctorCaseController extends ApiController
{
    use DoctorAccidentControllerTrait;

    /**
     * @return AccidentService
     */
    private function getAccidentService(): AccidentService
    {
        return $this->getServiceLocator()->get(AccidentService::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @todo I can manage this with a search method and filters in the future
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $sort = explode('|', $request->get('sort', 'createdAt|desc'));
        switch ($sort[0]) {
            case 'status':
                $sort[0] = 'accident_statuses.title';
                break;
            case 'city':
                $sort[0] = 'cities.title';
                break;
            case 'refNum':
                $sort[0] = 'accidents.ref_num';
                break;
            case 'createdAt':
            default:
                $sort[0] = 'accidents.created_at';
        }

        // @todo should be moved in service
        $accidents = Accident::select('accidents.*')
            ->join('accident_statuses', 'accidents.accident_status_id', '=', 'accident_statuses.id')
            ->join('doctor_accidents', 'accidents.caseable_id', '=', 'doctor_accidents.id')
            ->leftJoin('cities', 'accidents.city_id', '=', 'cities.id')
            // doctors accidents only
            ->where('accidents.caseable_type', DoctorAccident::class)
            // current doctor only
            ->where('doctor_accidents.doctor_id', $this->getDoctorId())
            // doctors status
            ->where('accident_statuses.type', AccidentStatusService::TYPE_DOCTOR)
            ->whereIn('accident_statuses.title', [
                AccidentStatusService::STATUS_IN_PROGRESS,
                AccidentStatusService::STATUS_ASSIGNED
            ])
            ->orderBy($sort[0], $sort[1])
            ->paginate($request->get('per_page', 10),
                $columns = ['*'], $pageName = 'page', $request->get('page', null));

        return $this->response->paginator($accidents, new DoctorCaseAccidentTransformer());
    }

    /**
     * Closed or accident which were sent which can't be changed
     * @param $id
     * @return Response
     * @throws InconsistentDataException
     */
    public function show(int $id): Response
    {
        /** @var Accident $accident */
        $accident = $this->getAccidentService()->first([AccidentService::FIELD_ID => $id]);
        if (!$accident) {
            $this->response->errorNotFound();
        }
        $this->checkAccess($accident);

        /** @var AccidentStatusService $accidentStatusesService */
        $accidentStatusesService = $this->getServiceLocator()->get(AccidentStatusService::class);
        $status = $accidentStatusesService->getDoctorInProgressStatus();
        if (
            $accident->getAttribute(AccidentService::FIELD_ACCIDENT_STATUS_ID)
            !== $status->getAttribute(AccidentStatusService::FIELD_ID)
        ) {
            $this->getAccidentService()->setStatus($accident, $status);
        }

        return $this->response->item($accident, new DoctorAccidentTransformer());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        Log::info('Request to update accident', ['id' => $id, 'data' => $request->toArray()]);

        /** @var Accident $accident */
        $accident = $this->getAccidentService()->first([AccidentService::FIELD_ID => $id]);
        $this->checkAccess($accident);

        $accident->accident_type_id = (int)$request->json('caseType', 1);
        $accident->caseable->recommendation = (string)$request->json('recommendation', '');
        $accident->caseable->investigation = (string)$request->json('investigation', '');

        $visitTime = $request->json('visitDateTime', '');
        $time = Carbon::parse($visitTime);
        $accident->caseable->visit_time = $time->format('Y-m-d H:i:s');
        $accident->caseable->save();
        $accident->save();

        // exclude saved by director (to not change their color)
        $diagnostics = $request->json('diagnostics', []);
        $accident->caseable->diagnostics()->detach();
        $accident->caseable->diagnostics()->attach($diagnostics);

        $services = $request->json('services', []);
        $accident->caseable->services()->detach();
        $accident->caseable->services()->attach($services);

        $surveys = $request->json('surveys', []);
        $accident->caseable->surveys()->detach();
        $accident->caseable->surveys()->attach($surveys);

        return $this->response->item($accident, new DoctorAccidentTransformer());
    }
}
