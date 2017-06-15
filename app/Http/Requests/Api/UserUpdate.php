<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Requests\Api;

use App\Role;
use App\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserUpdate extends UserStore
{
    public function validationData()
    {
        $data = parent::validationData();

        if(isset($data['id'])) {
            $user = User::find($data['id']);
            if (isset($data['email']) && $user->email == $data['email']) {
                unset($data['email']);
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
            'email' => 'email|unique:users',
            'name' => 'max:120',
            'phone' => 'max:30',
        ];
    }
}
