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
            'picture_url' => $doctor->hasMedia() ? $doctor->getMedia('photo')->first()->getUrl('thumb') : '',
            'city' => $doctor->city ? $doctor->city->title : '',
            'phones' => $doctor->user->phone,
            'mbn' => $doctor->medical_board_num,
            'lang' => $doctor->user->lang,
        ];
    }
}
