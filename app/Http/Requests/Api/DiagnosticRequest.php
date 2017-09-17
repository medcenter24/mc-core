<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Requests\Api;

use App\Role;

class DiagnosticRequest extends JsonRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::check()
            && (\Roles::hasRole(auth()->user(), Role::ROLE_DIRECTOR)
                || \Roles::hasRole(auth()->user(), Role::ROLE_DOCTOR));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|min:3',
            'commentary' => 'max:250',
        ];
    }
}
