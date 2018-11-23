<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Requests\Api;


use App\Role;
use Dingo\Api\Http\FormRequest;

abstract class JsonRequest extends FormRequest
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
     * The data to be validated should be processed as JSON.
     * @return mixed
     */
    protected function validationData()
    {

        $data = $this->json()->all();
        if (!$data || !count($data)) {
            $data = parent::validationData();
        }
        return $data;
    }
}
