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

use Dingo\Api\Http\Response;
use Illuminate\Support\Facades\Log;
use medcenter24\mcCore\App\Entity\AccidentType;
use medcenter24\mcCore\App\Http\Controllers\Api\ApiController;
use medcenter24\mcCore\App\Http\Requests\Api\AccidentTypeRequest;
use medcenter24\mcCore\App\Transformers\AccidentTypeTransformer;

class AccidentTypesController extends ApiController
{
    public function index(): Response
    {
        $types = AccidentType::orderBy('title')->get();
        return $this->response->collection($types, new AccidentTypeTransformer());
    }

    public function show($id): Response
    {
        $accidentType = AccidentType::findOrFail($id);
        return $this->response->item($accidentType, new AccidentTypeTransformer());
    }

    public function store(AccidentTypeRequest $request): Response
    {
        $accidentType = AccidentType::create([
            'title' => $request->json('title', ''),
            'description' => $request->json('description', ''),
        ]);
        $transformer = new AccidentTypeTransformer();
        return $this->response->created(null, $transformer->transform($accidentType));
    }

    public function update($id, AccidentTypeRequest $request): Response
    {
        $accidentType = AccidentType::findOrFail($id);
        $accidentType->title = $request->json('title', '');
        $accidentType->description = $request->json('description', '');
        $accidentType->save();
        Log::info('Accident type updated', [$accidentType, $this->user()]);
        return $this->response->item($accidentType, new AccidentTypeTransformer());
    }

    public function destroy($id): Response
    {
        $accidentType = AccidentType::findOrFail($id);
        Log::info('Accident type deleted', [$accidentType, $this->user()]);
        $accidentType->delete();
        return $this->response->noContent();
    }
}
