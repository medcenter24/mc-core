<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Requests;

use App\Role;
use Illuminate\Foundation\Http\FormRequest;

class StoreAccident extends FormRequest
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
            'created_by' => 'required|integer',
            'parent_id' => 'integer',
            'patient_id' => 'required|integer',
            'accident_type_id' => 'required|integer',
            'accident_status_id' => 'required|integer',
            'assistant_id' => 'integer',
            'assistant_ref_num' => 'string|max:255',
            'caseable_id' => 'required|integer',
            'caseable_type' => 'required|string',
            'ref_num' => 'required|string|max:70',
            'title' => 'required|max:200',
            'city_id' => 'integer',
            'address' => 'string',
            'contacts' => 'string',
            'symptoms' => 'string',
        ];
    }
}
