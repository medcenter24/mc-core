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

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use League\Fractal\TransformerAbstract;
use medcenter24\mcCore\App\Entity\Assistant;
use medcenter24\mcCore\App\Entity\City;
use medcenter24\mcCore\App\Entity\DatePeriod;
use medcenter24\mcCore\App\Entity\Doctor;
use medcenter24\mcCore\App\Entity\FinanceCondition;
use medcenter24\mcCore\App\Entity\Service;

class FinanceConditionTransformer extends TransformerAbstract
{

    private const MODEL_ASSISTANT = 'assistant';
    private const MODEL_DOCTOR = 'doctor';

    private const CONDITION_MODEL_MAP = [
        Assistant::class => self::MODEL_ASSISTANT,
        Doctor::class => self::MODEL_DOCTOR,
    ];

    /**
     * @param Model|FinanceCondition $financeCondition
     * @return array
     */
    public function transform(FinanceCondition $financeCondition): array
    {
        $assistantTransformer = new AssistantTransformer();
        $assistants = Assistant::whereIn('id', $financeCondition->conditions()
            ->where('model', Assistant::class)->get(['model_id'])
            ->map(static function($v) { return $v['model_id']; }))->get();
        $assistants = $assistants->map(static function($assistant) use($assistantTransformer) {
            return $assistantTransformer->transform($assistant);
        });

        $cityTransformer = new CityTransformer();
        $cities = City::whereIn('id', $financeCondition->conditions()
            ->where('model', City::class)->get(['model_id'])
            ->map(static function($v) { return $v['model_id']; }))->get();
        $cities = $cities->map(static function ($city) use ($cityTransformer) {
            return $cityTransformer->transform($city);
        });

        $doctorTransformer = new DoctorTransformer();
        $doctors = Doctor::whereIn('id', $financeCondition->conditions()
            ->where('model', Doctor::class)
            ->get(['model_id'])->map(static function($v) { return $v['model_id']; })
        )->get();
        $doctors = $doctors->map(static function ($doctor) use ($doctorTransformer) {
            return $doctorTransformer->transform($doctor);
        });

        $serviceTransformer = new ServiceTransformer();
        $services = Service::whereIn('id', $financeCondition->conditions()
            ->where('model', Service::class)->get(['model_id'])
            ->map(static function($v) { return $v['model_id']; })
        )->get();
        $services = $services->map(static function ($service) use ($serviceTransformer) {
            return $serviceTransformer->transform($service);
        });

        $datePeriodTransformer = new DatePeriodTransformer();
        $datePeriods = DatePeriod::whereIn('id', $financeCondition->conditions()
            ->where('model', DatePeriod::class)->get(['model_id'])
            ->map(static function($v) { return $v['model_id']; })
        )->get();
        $datePeriods = $datePeriods->map(static function ($period) use ($datePeriodTransformer) {
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
            'model' => $this->transformConditionModel($financeCondition->model),
            'currencyId' => (int) $financeCondition->currency_id,
            'currencyMode' => $financeCondition->currency_mode,
        ];
    }

    private function transformConditionModel(string $modelName): string
    {
        if (array_key_exists($modelName, self::CONDITION_MODEL_MAP)) {
            return self::CONDITION_MODEL_MAP[$modelName];
        }

        Log::error('Undefined finance condition model', [$modelName]);
        return 'undefined';
    }

    public function inverseTransformConditionModel(string $modelName): string
    {
        $invMap = array_flip(self::CONDITION_MODEL_MAP);

        if (array_key_exists($modelName, $invMap)) {
            return $invMap[$modelName];
        }

        Log::error('Undefined inverse finance condition model', [$modelName]);
        return 'undefined';
    }
}
