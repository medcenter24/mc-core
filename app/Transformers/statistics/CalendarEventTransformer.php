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

namespace medcenter24\mcCore\App\Transformers\statistics;


use medcenter24\mcCore\App\Accident;
use League\Fractal\TransformerAbstract;
use medcenter24\mcCore\App\Services\Core\ServiceLocator\ServiceLocatorTrait;
use medcenter24\mcCore\App\Services\UserService;

class CalendarEventTransformer extends TransformerAbstract
{
    use ServiceLocatorTrait;

    public function transform(Accident $accident): array
    {
        return [
            'id' => $accident->id,
            'title' => $accident->ref_num,
            'start' => $accident->created_at->setTimezone($this->getServiceLocator()
                ->get(UserService::class)->getTimezone())
                ->format(config('date.systemFormat')),
            'end' => $accident->visited_at ? $accident->visited_at->setTimezone($this->getServiceLocator()
                ->get(UserService::class)->getTimezone())
                ->format(config('date.systemFormat')) : '',
            'status' => $accident->accidentStatus ? $accident->accidentStatus->title : '',
        ];
    }
}
