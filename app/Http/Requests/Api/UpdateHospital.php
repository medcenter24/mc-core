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

namespace medcenter24\mcCore\App\Http\Requests\Api;

use medcenter24\mcCore\App\Hospital;

class UpdateHospital extends JsonRequest
{
    public function validationData()
    {
        $data = parent::validationData();

        // do not allow to change ref key
        if(isset($data['id'])) {
            $hospital = Hospital::find($data['id']);
            if (isset($data['refKey']) && $hospital->ref_key == $data['refKey']) {
                unset($data['refKey']);
            }
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
            'title' => 'min:1|max:150',
            'description' => 'max:255',
            'refKey' => 'min:1|max:5|unique:doctors',
            'address' => 'max:255',
            'phones' => 'max:200',
        ];
    }
}
