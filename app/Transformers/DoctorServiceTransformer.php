<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\DoctorService;
use League\Fractal\TransformerAbstract;

class DoctorServiceTransformer extends TransformerAbstract
{
    public function transform(DoctorService $service)
    {
        return [
            'id' => $service->id,
            'title' => $service->title,
            'description' => $service->description,
            'price' => $service->price,
        ];
    }
}
