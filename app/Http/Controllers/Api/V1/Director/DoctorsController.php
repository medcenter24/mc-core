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

use medcenter24\mcCore\App\City;
use medcenter24\mcCore\App\Doctor;
use medcenter24\mcCore\App\Http\Controllers\ApiController;
use medcenter24\mcCore\App\Http\Requests\Api\StoreDoctor;
use medcenter24\mcCore\App\Http\Requests\Api\UpdateDoctor;
use medcenter24\mcCore\App\Transformers\CityTransformer;
use medcenter24\mcCore\App\Transformers\DoctorTransformer;
use Dingo\Api\Http\Response;
use Illuminate\Http\Request;
use League\Fractal\TransformerAbstract;

class DoctorsController extends ApiController
{

    protected function getDataTransformer(): TransformerAbstract
    {
        return new DoctorTransformer();
    }

    protected function getModelClass(): string
    {
        return Doctor::class;
    }

    public function index(): Response
    {
        $doctors = Doctor::orderBy('name')->get();
        return $this->response->collection($doctors, new DoctorTransformer());
    }

    public function show($id): Response
    {
        $doctor = Doctor::findOrFail($id);
        return $this->response->item($doctor, new DoctorTransformer());
    }

    public function store(StoreDoctor $request): Response
    {
        $doctor = Doctor::create([
            'name' => $request->json('name', ''),
            'description' => $request->json('description', ''),
            'ref_key' => $request->json('refKey', ''),
            'medical_board_num' => $request->json('medicalBoardNumber', ''),
        ]);
        $transformer = new DoctorTransformer();
        return $this->response->created(null, $transformer->transform($doctor));
    }

    public function update($id, UpdateDoctor $request): Response
    {
        $doctor = Doctor::findOrFail($id);
        $doctor->name = $request->json('name', '');
        $doctor->ref_key = $request->json('refKey', '');
        $doctor->user_id = (int)$request->json('userId', 0);
        $doctor->description = $request->json('description', '');
        $doctor->medical_board_num = $request->json('medicalBoardNumber', '');
        $doctor->save();

        \Log::info('Doctor updated', [$doctor]);

        return $this->response->item($doctor, new DoctorTransformer());
    }

    public function destroy($id): Response
    {
        $doctor = Doctor::findOrFail($id);
        \Log::info('Doctor deleted', [$doctor]);
        $doctor->delete();
        return $this->response->noContent();
    }

    /**
     * Covered by doctor
     * @param $id
     * @return Response
     */
    public function cities($id): Response
    {
        $doctor = Doctor::findOrFail($id);
        return $this->response->collection($doctor->cities, new CityTransformer());
    }

    public function setCities($id, Request $request): Response
    {
        $doctor = Doctor::findOrFail($id);
        $doctor->cities()->detach();
        $cities = $request->json('cities', []);
        if (count($cities)) {
            $doctor->cities()->attach($cities);
        }
        return $this->response->accepted();
    }

    public function getDoctorsByCity($cityId): Response
    {
        $city = City::findOrFail($cityId);
        return $this->response->collection($city->doctors, new DoctorTransformer());
    }
}
