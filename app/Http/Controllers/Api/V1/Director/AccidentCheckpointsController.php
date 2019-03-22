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

namespace App\Http\Controllers\Api\V1\Director;

use App\AccidentCheckpoint;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\AccidentCheckpointRequest;
use App\Transformers\AccidentCheckpointTransformer;
use Dingo\Api\Http\Response;
use League\Fractal\TransformerAbstract;

class AccidentCheckpointsController extends ApiController
{
    protected function getDataTransformer(): TransformerAbstract
    {
        return new AccidentCheckpointTransformer();
    }

    protected function getModelClass(): string
    {
        return AccidentCheckpoint::class;
    }

    public function index(): Response
    {
        $accidentCheckpoint = AccidentCheckpoint::orderBy('title')->get();
        return $this->response->collection($accidentCheckpoint, new AccidentCheckpointTransformer());
    }

    public function show($id): Response
    {
        $accidentCheckpoint = AccidentCheckpoint::findOrFail($id);
        return $this->response->item($accidentCheckpoint, new AccidentCheckpointTransformer());
    }

    public function store(AccidentCheckpointRequest $request): Response
    {
        $accidentCheckpoint = AccidentCheckpoint::create([
            'title' => $request->json('title', ''),
            'description' => $request->json('description', ''),
        ]);
        $transformer = new AccidentCheckpointTransformer();
        return $this->response->created(null, $transformer->transform($accidentCheckpoint));
    }

    public function update($id, AccidentCheckpointRequest $request): Response
    {
        $accidentCheckpoint = AccidentCheckpoint::findOrFail($id);
        $accidentCheckpoint->title = $request->json('title', '');
        $accidentCheckpoint->description = $request->json('description', '');
        $accidentCheckpoint->save();
        \Log::info('Accident status updated', [$accidentCheckpoint, $this->user()]);
        $this->response->item($accidentCheckpoint, new AccidentCheckpointTransformer());
    }

    public function destroy($id): Response
    {
        $accidentCheckpoint = AccidentCheckpoint::findOrFail($id);
        \Log::info('Accident status deleted', [$accidentCheckpoint, $this->user()]);
        $accidentCheckpoint->delete();
        return $this->response->noContent();
    }
}
