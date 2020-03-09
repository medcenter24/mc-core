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

namespace medcenter24\mcCore\App\Transformers;

use League\Fractal\TransformerAbstract;
use medcenter24\mcCore\App\Entity\Assistant;
use medcenter24\mcCore\App\Entity\City;
use medcenter24\mcCore\App\Entity\DatePeriod;
use medcenter24\mcCore\App\Entity\Doctor;
use medcenter24\mcCore\App\Entity\Service;
use medcenter24\mcCore\App\Entity\FinanceCondition;

class FinanceConditionTransformer extends TransformerAbstract
{
    public function transform(FinanceCondition $financeCondition): array
    {
        $assistantTransformer = new AssistantTransformer();
        $assistants = Assistant::whereIn('id', $financeCondition->conditions()
            ->where('model', Assistant::class)->get(['model_id'])
            ->map(function($v) { return $v['model_id']; }))->get();
        $assistants = $assistants->map(function($assistant) use($assistantTransformer) {
            return $assistantTransformer->transform($assistant);
        });

        $cityTransformer = new CityTransformer();
        $cities = City::whereIn('id', $financeCondition->conditions()
            ->where('model', City::class)->get(['model_id'])
            ->map(function($v) { return $v['model_id']; }))->get();
        $cities = $cities->map(function ($city) use ($cityTransformer) {
            return $cityTransformer->transform($city);
        });

        $doctorTransformer = new DoctorTransformer();
        $doctors = Doctor::whereIn('id', $financeCondition->conditions()
            ->where('model', Doctor::class)
            ->get(['model_id'])->map(function($v) { return $v['model_id']; })
        )->get();
        $doctors = $doctors->map(function ($doctor) use ($doctorTransformer) {
            return $doctorTransformer->transform($doctor);
        });

        $serviceTransformer = new ServiceTransformer();
        $services = Service::whereIn('id', $financeCondition->conditions()
            ->where('model', Service::class)->get(['model_id'])
            ->map(function($v) { return $v['model_id']; })
        )->get();
        $services = $services->map(function ($service) use ($serviceTransformer) {
            return $serviceTransformer->transform($service);
        });

        $datePeriodTransformer = new DatePeriodTransformer();
        $datePeriods = DatePeriod::whereIn('id', $financeCondition->conditions()
            ->where('model', DatePeriod::class)->get(['model_id'])
            ->map(function($v) { return $v['model_id']; })
        )->get();
        $datePeriods = $datePeriods->map(function ($period) use ($datePeriodTransformer) {
            return $datePeriodTransformer->transform($period);
        });

        return [
            'id' => (int) $financeCondition->id,
            'title' => $financeCondition->title,
            'value' => $financeCondition->value,
            'assistants' => $assistants,
            'cities' => $cities,
            'doctors' => $doctors,
            'services' => $services,
            'datePeriods' => $datePeriods,
            'type' => $financeCondition->type,
            'model' => $financeCondition->model,
            'currencyId' => (int) $financeCondition->currency_id,
            'currencyMode' => $financeCondition->currency_mode,
        ];
    }
}
