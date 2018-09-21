<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\Assistant;
use App\City;
use App\DatePeriod;
use App\Doctor;
use App\DoctorService;
use App\FinanceCondition;
use League\Fractal\TransformerAbstract;

class FinanceConditionTransformer extends TransformerAbstract
{
    public function transform(FinanceCondition $financeCondition)
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

        $serviceTransformer = new DoctorServiceTransformer();
        $services = DoctorService::whereIn('id', $financeCondition->conditions()
            ->where('model', DoctorService::class)->get(['model_id'])
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
            'id' => $financeCondition->id,
            'title' => $financeCondition->title,
            'priceAmount' => $financeCondition->price,
            'assistants' => $assistants,
            'cities' => $cities,
            'doctors' => $doctors,
            'services' => $services,
            'datePeriods' => $datePeriods,
        ];
    }
}
