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

namespace medcenter24\mcCore\App\Http\Requests\Api;

use Illuminate\Support\Facades\Auth;
use medcenter24\mcCore\App\Services\Entity\RoleService;
use medcenter24\mcCore\App\Support\Facades\Roles;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserStore extends JsonRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Auth::check()
            && (Roles::hasRole(auth()->user(), RoleService::DIRECTOR_ROLE)
                || Roles::hasRole(auth()->user(), RoleService::DOCTOR_ROLE));
    }

    /**
     * @return array
     */
    public function validationData(): array
    {
        $data = parent::validationData();

        $id = (int) $data['id'];
        // if doctor access - he can change only his data
        if (auth()->user()->id !== $id
            && !Roles::hasRole(auth()->user(), RoleService::DIRECTOR_ROLE)
            && Roles::hasRole(auth()->user(), RoleService::DOCTOR_ROLE)
        ) {
            throw new HttpException(403);
        }

        return $data;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users',
            'name' => 'max:120',
            'phone' => 'max:30',
            'password' => 'required',
        ];
    }
}
