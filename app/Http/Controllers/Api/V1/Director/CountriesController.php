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

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Director;

use Dingo\Api\Http\Response;
use medcenter24\mcCore\App\Country;
use medcenter24\mcCore\App\Http\Controllers\ApiController;
use medcenter24\mcCore\App\Http\Requests\Api\CountryRequest;
use medcenter24\mcCore\App\Transformers\CountryTransformer;
use League\Fractal\TransformerAbstract;

class CountriesController extends ApiController
{
    protected function getDataTransformer(): TransformerAbstract
    {
        return new CountryTransformer();
    }

    protected function getModelClass(): string
    {
        return Country::class;
    }

    public function index(): Response
    {
        $countries = Country::orderBy('title')->get();
        return $this->response->collection($countries, new CountryTransformer());
    }

    public function store(CountryRequest $request): Response
    {
        $country = Country::create([
            'title' => $request->json('title', ''),
        ]);
        $transformer = new CountryTransformer();
        return $this->response->created(null, $transformer->transform($country));
    }

    public function update($id, CountryRequest $request): Response
    {
        $country = Country::findOrFail($id);
        $country->title = $request->json('title', '');
        $country->save();

        \Log::info('Country updated', [$country, $this->user()]);
        return $this->response->item($country, new CountryTransformer());
    }

    public function destroy($id): Response
    {
        $country = Country::findOrFail($id);
        \Log::info('Country deleted', [$country, $this->user()]);
        $country->delete();
        return $this->response->noContent();
    }
}
