<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Requests;

use App\Role;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorService extends FormRequest
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
            'created_by' => 'integer',
            'title' => 'max:255',
            'description' => 'max:255',
            'price' => 'between:0,10000.99',
        ];
    }
}