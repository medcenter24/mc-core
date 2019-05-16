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

use medcenter24\mcCore\App\Accident;
use medcenter24\mcCore\App\AccidentStatus;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Http\Controllers\ApiController;
use medcenter24\mcCore\App\Http\Requests\StoreAccident;
use medcenter24\mcCore\App\Http\Requests\UpdateAccident;
use medcenter24\mcCore\App\Services\AccidentService;
use medcenter24\mcCore\App\Services\AccidentStatusesService;
use medcenter24\mcCore\App\Transformers\AccidentTransformer;
use Dingo\Api\Http\Response;
use League\Fractal\TransformerAbstract;

class AccidentsController extends ApiController
{
    protected function getDataTransformer(): TransformerAbstract
    {
        return new AccidentTransformer();
    }

    protected function getModelClass(): string
    {
        return Accident::class;
    }

    public function index(): Response
    {
        $accidents = Accident::orderBy('created_at', 'desc')->get();
        return $this->response->collection($accidents, new AccidentTransformer());
    }

    /**
     * @param StoreAccident $request
     * @param AccidentService $accidentService
     * @param AccidentStatusesService $accidentStatusesService
     * @return Response
     * @throws InconsistentDataException
     */
    public function store(StoreAccident $request, AccidentService $accidentService, AccidentStatusesService $accidentStatusesService): Response
    {
        /** @var Accident $accident */
        $accident = $accidentService->create($request->all());
        $accidentService->setStatus($accident, $accidentStatusesService->getNewStatus());
        return $this->response->created('', ['id' => $accident->getAttribute('id')]);
    }

    public function show($id): Response
    {
        $accident = Accident::find($id);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        return $this->response->item($accident, new AccidentTransformer());
    }

    public function update(UpdateAccident $request, $id): array
    {
        /** @var \Eloquent $accident */
        $accident = Accident::findOrFail($id);
        foreach ($accident->getVisible() as $item) {
            if ($request->has($item)) {
                $accident->$item = $request->get($item);
            }
        }
        $accident->save();

        return ['success' => true];
    }

    public function destroy($id): array
    {
        Accident::destroy($id);
        return ['success' => true];
    }
}
