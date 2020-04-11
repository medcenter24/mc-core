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

use medcenter24\mcCore\App\Entity\User;

class UserUpdate extends UserStore
{
    private $requireEmailRule = 'required|';

    public function validationData(): array
    {
        $data = parent::validationData();

        if(isset($data['id'])) {
            $user = User::find($data['id']);
            if ($user && isset($data['email']) && $user->email === $data['email']) {
                unset($data['email']);
                $this->requireEmailRule = '';
            }
            $this->requireEmailRule .= '|unique:users,' . $data['id'] . '|';
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
            'email' => $this->requireEmailRule . 'email',
            'name' => 'max:120',
            'phone' => 'max:30',
        ];
    }
}
