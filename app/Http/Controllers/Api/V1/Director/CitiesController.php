<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
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
