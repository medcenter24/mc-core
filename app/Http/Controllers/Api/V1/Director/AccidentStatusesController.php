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

use medcenter24\mcCore\App\AccidentStatus;
use medcenter24\mcCore\App\Http\Controllers\ApiController;
use medcenter24\mcCore\App\Http\Requests\Api\AccidentStatusRequest;
use medcenter24\mcCore\App\Transformers\AccidentStatusTransformer;

class AccidentStatusesController extends ApiController
{
    public function index()
    {
        $accidentStatus = AccidentStatus::orderBy('title')->get();
        return $this->response->collection($accidentStatus, new AccidentStatusTransformer());
    }

    public function show($id)
    {
        $accidentStatus = AccidentStatus::findOrFail($id);
        return $this->response->item($accidentStatus, new AccidentStatusTransformer());
    }

    public function store(AccidentStatusRequest $request)
    {
        $accidentStatus = AccidentStatus::create([
            'title' => $request->json('title', ''),
            'type' => $request->json('type', ''),
        ]);
        $transformer = new AccidentStatusTransformer();
        return $this->response->created(null, $transformer->transform($accidentStatus));
    }

    public function update($id, AccidentStatusRequest $request)
    {
        $accidentStatus = AccidentStatus::findOrFail($id);
        $accidentStatus->title = $request->json('title', '');
        $accidentStatus->type = $request->json('type', '');
        $accidentStatus->save();
        \Log::info('Accident status updated', [$accidentStatus, $this->user()]);
        $this->response->item($accidentStatus, new AccidentStatusTransformer());
    }

    public function destroy($id)
    {
        $accidentStatus = AccidentStatus::findOrFail($id);
        \Log::info('Accident status deleted', [$accidentStatus, $this->user()]);
        $accidentStatus->delete();
        return $this->response->noContent();
    }
}
