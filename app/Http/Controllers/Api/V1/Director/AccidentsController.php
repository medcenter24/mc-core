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

use medcenter24\mcCore\App\Contract\General\Service\ModelService;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Http\Controllers\Api\ModelApiController;
use medcenter24\mcCore\App\Http\Requests\StoreAccident;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\AccidentStatusService;
use medcenter24\mcCore\App\Transformers\AccidentTransformer;
use Dingo\Api\Http\Response;
use League\Fractal\TransformerAbstract;

/**
 * List of accidents to assign as a parent to the case
 *
 * Class AccidentsController
 * @package medcenter24\mcCore\App\Http\Controllers\Api\V1\Director
 */
class AccidentsController extends ModelApiController
{
    protected function getDataTransformer(): TransformerAbstract
    {
        return new AccidentTransformer();
    }

    protected function getModelClass(): string
    {
        return Accident::class;
    }

    /**
     * @param StoreAccident $request
     * @param AccidentService $accidentService
     * @param AccidentStatusService $accidentStatusesService
     * @return Response
     * @throws InconsistentDataException
     * todo why do I need this?
     */
/*    public function store(StoreAccident $request, AccidentService $accidentService, AccidentStatusService $accidentStatusesService): Response
    {

        $accident = $accidentService->create($request->all());
        $accidentService->setStatus($accident, $accidentStatusesService->getNewStatus());
        return $this->response->created('', ['id' => $accident->getAttribute('id')]);
    }*/

    /**
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        /** @var AccidentService $accidentService */
        $accidentService = $this->getServiceLocator()->get(AccidentService::class);
        $accident = $accidentService->first([AccidentService::FIELD_ID => $id]);
        if (!$accident) {
            $this->response->errorNotFound();
        }

        return $this->response->item($accident, new AccidentTransformer());
    }

    /*
     * @todo do I need it?
     * public function update(UpdateAccident $request, $id): array
    {
        $accident = Accident::findOrFail($id);
        foreach ($accident->getVisible() as $item) {
            if ($request->has($item)) {
                $accident->$item = $request->get($item);
            }
        }
        $accident->save();

        return ['success' => true];
    }*/

    /*public function destroy($id): array
    {
        Accident::destroy($id);
        return ['success' => true];
    }*/

    /**
     * @inheritDoc
     */
    protected function getModelService(): ModelService
    {
        return $this->getServiceLocator()->get(AccidentService::class);
    }
}
