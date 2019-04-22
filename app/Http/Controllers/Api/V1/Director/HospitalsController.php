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

use medcenter24\mcCore\App\Hospital;
use medcenter24\mcCore\App\Http\Controllers\ApiController;
use medcenter24\mcCore\App\Http\Requests\Api\StoreHospital;
use medcenter24\mcCore\App\Http\Requests\Api\UpdateHospital;
use medcenter24\mcCore\App\Transformers\HospitalTransformer;
use League\Fractal\TransformerAbstract;

class HospitalsController extends ApiController
{
    protected function getDataTransformer(): TransformerAbstract
    {
        return new HospitalTransformer();
    }

    protected function getModelClass(): string
    {
        return Hospital::class;
    }

    public function index()
    {
        $hospitals = Hospital::orderBy('title')->get();
        return $this->response->collection($hospitals, new HospitalTransformer());
    }

    public function show($id)
    {
        $hospital = Hospital::findOrFail($id);
        return $this->response->item($hospital, new HospitalTransformer());
    }

    public function store(StoreHospital $request)
    {
        $hospital = Hospital::create([
            'title' => $request->json('title', ''),
            'description' => $request->json('description', ''),
            'address' => $request->json('address', ''),
            'phones' => $request->json('phones', ''),
            'ref_key' => $request->json('refKey', ''),
        ]);
        $transformer = new HospitalTransformer();
        return $this->response->created(null, $transformer->transform($hospital));
    }

    public function update($id, UpdateHospital $request)
    {
        $hospital = Hospital::findOrFail($id);
        $hospital->title = $request->json('title', '');
        $hospital->ref_key = $request->json('refKey', '');
        $hospital->address = $request->json('address', '');
        $hospital->description = $request->json('description', '');
        $hospital->phones = $request->json('phones', '');
        $hospital->save();

        \Log::info('Hospital updated', [$hospital, $this->user()]);

        return $this->response->item($hospital, new HospitalTransformer());
    }

    public function destroy($id)
    {
        $hospital = Hospital::findOrFail($id);
        \Log::info('Hospital deleted', [$hospital, $this->user()]);
        $hospital->delete();
        return $this->response->noContent();
    }
}
