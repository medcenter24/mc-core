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

namespace medcenter24\mcCore\App\Http\Controllers\Api\V1\Doctor;

use Dingo\Api\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use medcenter24\mcCore\App\Entity\Doctor;
use medcenter24\mcCore\App\Entity\User;
use medcenter24\mcCore\App\Http\Controllers\Api\ApiController;
use medcenter24\mcCore\App\Services\LogoService;
use medcenter24\mcCore\App\Transformers\DoctorProfileTransformer;
use medcenter24\mcCore\App\Transformers\UserTransformer;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;

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
            Log::warning('User has role doctor but has not an assigned doctor', ['user' => ['id' => $this->user()->id, 'name' => $this->user()->name]]);
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

    /**
     * Load photo
     * @return Response
     */
    public function photo(): Response
    {
        /** @var User $user */
        $user = $this->user();

        $user->clearMediaCollection(LogoService::FOLDER);
        try {
            $user->addMediaFromRequest('files')
                ->toMediaCollection(LogoService::FOLDER, LogoService::DISC);
        } catch (FileCannotBeAdded $e) {
            if (stripos($e->getMessage(), 'unlink(') === false) {
                $this->response->error($e->getMessage(), 500);
            }
        }

        return $this->response->item($user->doctor, new DoctorProfileTransformer());
    }
}
