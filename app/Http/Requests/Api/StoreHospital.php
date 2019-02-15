<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Requests\Api;

class StoreHospital extends JsonRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|max:150',
            'description' => 'max:255',
            'refKey' => 'required|max:5|unique:doctors',
            'address' => 'max:255',
            'phones' => 'max:200',
        ];
    }
}
