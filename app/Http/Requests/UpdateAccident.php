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

namespace medcenter24\mcCore\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use medcenter24\mcCore\App\Services\Entity\RoleService;

class UpdateAccident extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::check() && \Roles::hasRole(auth()->user(), RoleService::DIRECTOR_ROLE);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'parent_id' => 'integer',
            'accident_type_id' => 'integer|min:1',
            'accident_status_id' => 'integer|min:1',
            'ref_num' => 'string|between:1,70',
            'title' => 'string|between:1,200',
            'city_id' => 'integer',
            'address' => 'string',
            'contacts' => 'string',
            'symptoms' => 'string',
        ];
    }
}
