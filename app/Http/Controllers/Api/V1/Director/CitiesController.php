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
use medcenter24\mcCore\App\Entity\City;
use medcenter24\mcCore\App\Http\Controllers\Api\ModelApiController;
use medcenter24\mcCore\App\Http\Requests\Api\CityRequest;
use medcenter24\mcCore\App\Transformers\CityTransformer;
use League\Fractal\TransformerAbstract;

class CitiesController extends ModelApiController
{
    protected function getDataTransformer(): TransformerAbstract
    {
        return new CityTransformer();
    }

    protected function getModelClass(): string
    {
        return City::class;
    }

    public function index(): Response
    {
        $cities = City::orderBy('title')->get();
        return $this->response->collection($cities, new CityTransformer());
    }

    public function store(CityRequest $request): Response
    {
        $city = City::create([
            'title' => $request->json('title', ''),
            'region_id' => $request->json('regionId', 0),
        ]);
        $transformer = new CityTransformer();
        return $this->response->created(null, $transformer->transform($city));
    }

    public function update($id, CityRequest $request): Response
    {
        $city = City::findOrFail($id);
        $city->title = $request->json('title', '');
        $city->region_id = $request->json('regionId', 0);
        $city->save();

        Log::info('City updated', [$city, $this->user()]);
        return $this->response->item($city, new CityTransformer());
    }

    public function destroy($id): Response
    {
        $city = City::findOrFail($id);
        Log::info('City deleted', [$city, $this->user()]);
        $city->delete();
        return $this->response->noContent();
    }
}
