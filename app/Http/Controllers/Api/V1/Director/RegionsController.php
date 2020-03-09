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
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director;

use Illuminate\Support\Facades\Log;
use Dingo\Api\Http\Response;
use League\Fractal\TransformerAbstract;
use medcenter24\mcCore\App\Http\Controllers\Api\ModelApiController;
use medcenter24\mcCore\App\Http\Requests\Api\RegionRequest;
use medcenter24\mcCore\App\Entity\Region;
use medcenter24\mcCore\App\Transformers\RegionTransformer;

class RegionsController extends ModelApiController
{
    protected function getDataTransformer(): TransformerAbstract
    {
        return new RegionTransformer();
    }

    protected function getModelClass(): string
    {
        return Region::class;
    }

    public function index(): Response
    {
        $regions = Region::orderBy('title')->get();
        return $this->response->collection($regions, new RegionTransformer());
    }

    public function store(RegionRequest $request): Response
    {
        $country = Region::create([
            'title' => $request->json('title', ''),
            'country_id' => $request->json('countryId', ''),
        ]);
        $transformer = new RegionTransformer();
        return $this->response->created(null, $transformer->transform($country));
    }

    public function update($id, RegionRequest $request): Response
    {
        $region = Region::findOrFail($id);
        $region->title = $request->json('title', '');
        $region->country_id = $request->json('countryId', '');
        $region->save();

        Log::info('Region updated', [$region, $this->user()]);
        return $this->response->item($region, new RegionTransformer());
    }

    public function destroy($id): Response
    {
        $region = Region::findOrFail($id);
        Log::info('Region deleted', [$region, $this->user()]);
        $region->delete();
        return $this->response->noContent();
    }
}
