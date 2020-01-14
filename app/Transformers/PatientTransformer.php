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


use medcenter24\mcCore\App\Helpers\Date;
use medcenter24\mcCore\App\Patient;
use medcenter24\mcCore\App\Services\UserService;

class PatientTransformer extends AbstractTransformer
{
    public function transform(Patient $patient): array
    {
        return [
            'id' => $patient->id,
            'name' => $patient->name,
            'address' => $patient->address,
            'phones' => $patient->phones,
            'birthday' => Date::sysDate(
                $patient->birthday,
                $this->getServiceLocator()->get(UserService::class)->getTimezone()
            ),
            'comment' => $patient->comment
        ];
    }
}
