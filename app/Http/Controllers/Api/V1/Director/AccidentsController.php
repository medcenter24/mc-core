<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\Accident;
use App\AccidentStatus;
use App\Http\Controllers\ApiController;
use App\Http\Requests\StoreAccident;
use App\Http\Requests\UpdateAccident;
use App\Services\AccidentStatusesService;
use App\Transformers\AccidentTransformer;
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

    public function store(StoreAccident $request, AccidentStatusesService $statusesService)
    {
        $accident = Accident::create($request->all());

        $statusesService->set($accident, AccidentStatus::findOrCreate([
            'title' => AccidentStatusesService::STATUS_NEW,
            'type' => AccidentStatusesService::TYPE_ACCIDENT,
        ]));
        return $accident;
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
