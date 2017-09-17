<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Requests\Api;

use App\Hospital;

class UpdateHospital extends JsonRequest
{
    public function validationData()
    {
        $data = parent::validationData();

        if(isset($data['id'])) {
            $hospital = Hospital::find($data['id']);
            if (isset($data['ref_key']) && $hospital->ref_key == $data['ref_key']) {
                unset($data['ref_key']);
            }
        }
        return $data;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'min:1|max:150',
            'description' => 'max:255',
            'ref_key' => 'min:1|max:5|unique:doctors',
            'address' => 'max:255',
            'phones' => 'max:200',
        ];
    }
}
