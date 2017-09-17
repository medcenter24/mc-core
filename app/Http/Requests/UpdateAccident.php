<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Requests;

use App\Role;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAccident extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::check() && \Roles::hasRole(auth()->user(), Role::ROLE_DIRECTOR);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'parent_id' => 'integer',
            'accident_type_id' => 'integer|min:1',
            'accident_status_id' => 'integer|min:1',
            'ref_num' => 'string|between:1,70',
            'title' => 'string|between:1,200',
            'city_id' => 'integer',
            'address' => 'string',
            'contacts' => 'string',
            'symptoms' => 'string',
        ];
    }
}
