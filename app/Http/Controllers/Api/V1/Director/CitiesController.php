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

use App\City;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\CityRequest;
use App\Transformers\CityTransformer;
use League\Fractal\TransformerAbstract;

class CitiesController extends ApiController
{
    protected function getDataTransformer(): TransformerAbstract
    {
        return new CityTransformer();
    }

    protected function getModelClass(): string
    {
        return City::class;
    }

    public function index()
    {
        $cities = City::orderBy('title')->get();
        return $this->response->collection($cities, new CityTransformer());
    }

    public function store(CityRequest $request)
    {
        $city = City::create([
            'title' => $request->json('title', ''),
        ]);
        $transformer = new CityTransformer();
        return $this->response->created(null, $transformer->transform($city));
    }

    public function update($id, CityRequest $request)
    {
        $city = City::findOrFail($id);
        $city->title = $request->json('title', '');
        $city->save();

        \Log::info('City updated', [$city, $this->user()]);
        return $this->response->item($city, new CityTransformer());
    }

    public function destroy($id)
    {
        $city = City::findOrFail($id);
        \Log::info('City deleted', [$city, $this->user()]);
        $city->delete();
        return $this->response->noContent();
    }
}
