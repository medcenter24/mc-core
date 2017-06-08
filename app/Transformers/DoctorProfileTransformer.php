<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\Doctor;
use League\Fractal\TransformerAbstract;

class DoctorProfileTransformer extends TransformerAbstract
{
    public function transform (Doctor $doctor)
    {
        return [
            'name' => $doctor->name,
            'picture_url' => 'asdf', // $doctor->hasMedia() ? $doctor->getMedia('photo')->first()->getUrl('thumb') : 'plumb',
            'city' => $doctor->city->title,
            'phones' => '+375255283638', // $doctor->phones
            'mbn' => $doctor->medical_board_num
        ];
    }
}
