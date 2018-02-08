<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Requests\Telegram;

use App\Role;
use App\Support\Facades\Roles;
use Illuminate\Foundation\Http\FormRequest;

class SetWebhook extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Roles::hasRole(auth()->user(), Role::ROLE_ADMIN);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'webhook' => 'required|url'
        ];
    }
}
