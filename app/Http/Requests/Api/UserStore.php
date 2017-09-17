<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Requests\Api;

use App\Role;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserStore extends JsonRequest
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

    public function validationData()
    {
        $data = parent::validationData();

        // if doctor access - he can change only his data
        if (\Roles::hasRole(auth()->user(), Role::ROLE_DOCTOR) && auth()->user()->id != $data['id']) {
            throw new HttpException(403);
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
            'email' => 'required|email|unique:users',
            'name' => 'max:120',
            'phone' => 'max:30',
        ];
    }
}
