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

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor;


use Dingo\Api\Http\Response;
use Illuminate\Http\Request;
use medcenter24\mcCore\App\Doctor;
use medcenter24\mcCore\App\Http\Controllers\ApiController;
use medcenter24\mcCore\App\Transformers\DoctorProfileTransformer;

class ProfileController extends ApiController
{
    /**
     * Get information about logged user
     * @return Response
     */
    public function me(): Response
    {
        $doctor = $this->user()->doctor;
        if (!$doctor) {
            \Log::warning('User has role doctor but has not an assigned doctor', ['user' => ['id' => $this->user()->id, 'name' => $this->user()->name]]);
            $this->response->errorBadRequest('User is not a doctor');
        }

        return $this->response->item(
            $this->user()->doctor,
            new DoctorProfileTransformer()
        );
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function update(Request $request): Response
    {
        /** @var Doctor $doctor */
        $doctor = $this->user()->getAttribute('doctor');
        $name = $request->get('name','');
        if ($name) {
            $doctor->setAttribute('name', $name);
            $doctor->save();
        }

        $phones = $request->get('phones', '');
        if ($phones) {
            $user = $doctor->getAttribute('user');
            $user->setAttribute('phone', $phones);
            $user->save();
        }

        return $this->response->item(
            $doctor,
            new DoctorProfileTransformer()
        );
    }

    /**
     * Change language for the logged user
     * @param $lang
     * @return Response
     */
    public function lang($lang): Response
    {
        $user = $this->user();
        $user->lang = $lang;
        $user->save();
        return $this->response->noContent();
    }
}
