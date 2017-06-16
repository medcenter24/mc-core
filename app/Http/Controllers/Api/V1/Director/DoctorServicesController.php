<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Director;

use App\DoctorService;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\DoctorServiceRequest;
use App\Transformers\DoctorServiceTransformer;

class DoctorServicesController extends ApiController
{
    public function index()
    {
        $services = DoctorService::orderBy('title', 'desc')->get();
        return $this->response->collection($services, new DoctorServiceTransformer());
    }

    public function update($id, DoctorServiceRequest $request)
    {
        $doctorService = DoctorService::find($id);
        if (!$doctorService) {
            $this->response->errorNotFound();
        }

        $doctorService->title= $request->json('title', '');
        $doctorService->description = $request->json('description', '');
        $doctorService->price = $request->json('price', 0);
        $doctorService->save();

        $transformer = new DoctorServiceTransformer();
        return $this->response->accepted(null, $transformer->transform($doctorService));
    }

    public function store(DoctorServiceRequest $request)
    {
        $doctorService = DoctorService::create([
            'title' => $request->json('title', ''),
            'description' => $request->json('description', ''),
            'price' => $request->json('price', 0),
        ]);
        $transformer = new DoctorServiceTransformer();
        return $this->response->created(null, $transformer->transform($doctorService));
    }
    
    public function destroy($id)
    {
        $service = DoctorService::find($id);
        if (!$service) {
            $this->response->errorNotFound();
        }
        $service->delete();
        return $this->response->noContent();
    }
}
