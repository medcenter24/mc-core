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

namespace medcenter24\mcCore\App\Transformers;


use medcenter24\mcCore\App\Doctor;

class DoctorProfileTransformer extends AbstractTransformer
{
    public function transform (Doctor $doctor): array
    {
        return [
            'name' => $doctor->name,
            'picture_url' => $doctor->hasMedia() ? $doctor->getMedia('photo')->first()->getUrl('thumb') : '',
            'city' => $doctor->city ? $doctor->city->title : '',
            'phones' => $doctor->user->phone,
            'mbn' => $doctor->medical_board_num,
            'lang' => $doctor->user->lang,
        ];
    }
}
