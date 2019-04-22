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


use medcenter24\mcCore\App\DoctorAccident;
use InvalidArgumentException;
use League\Fractal\TransformerAbstract;

class DoctorCaseTransformer extends TransformerAbstract
{
    public function transform(DoctorAccident $doctorAccident)
    {
        try {
            $visitTime = $doctorAccident->visit_time
                ? $doctorAccident->visit_time
                    ->setTimezone(auth()->user()->timezone)
                    ->format(config('date.systemFormat'))
                : '';
        } catch (InvalidArgumentException $e) {
            $visitTime = '';
        }
        return [
            'id' => $doctorAccident->id,
            'doctorId' => $doctorAccident->doctor_id,
            'city_id' => $doctorAccident->accident->city_id,
            // api uses only system format if we need to convert it - do it at the frontend
            'createdAt' => $doctorAccident->created_at->setTimezone(auth()->user()->timezone)->format(config('date.systemFormat')),
            'visitTime' => $visitTime,
            'recommendation' => $doctorAccident->recommendation,
            'investigation' => $doctorAccident->investigation,
        ];
    }
}
