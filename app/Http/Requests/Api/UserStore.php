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

namespace App\Http\Requests\Api;

use App\Role;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserStore extends JsonRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::check()
            && (\Roles::hasRole(auth()->user(), Role::ROLE_DIRECTOR)
                || \Roles::hasRole(auth()->user(), Role::ROLE_DOCTOR));
    }

    public function validationData()
    {
        $data = parent::validationData();

        // if doctor access - he can change only his data
        if (\Roles::hasRole(auth()->user(), Role::ROLE_DOCTOR) && auth()->user()->id != $data['id']) {
            throw new HttpException(403);
        }

        return $data;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email|unique:users',
            'name' => 'max:120',
            'phone' => 'max:30',
            'password' => 'required',
        ];
    }
}
