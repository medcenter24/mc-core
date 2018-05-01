<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Requests\Api;

use App\Doctor;
use App\Role;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UpdateDoctor extends JsonRequest
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

        if(isset($data['id'])) {
            $doc = Doctor::find($data['id']);
            if (isset($data['refKey']) && $doc->ref_key == $data['refKey']) {
                unset($data['refKey']);
            }
        }

        // if doctor access - he can change only his data
        if (\Roles::hasRole(auth()->user(), Role::ROLE_DOCTOR) && auth()->user()->doctor->id != $data['id']) {
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
            'name' => 'min:1|max:150',
            'description' => 'min:1|max:255',
            'refKey' => 'min:1|max:5|unique:doctors',
        ];
    }
}
