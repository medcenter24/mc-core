<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Http\Controllers\Api\V1\Doctor;

use App\Http\Controllers\ApiController;
use App\Transformers\DoctorProfileTransformer;


class ProfileController extends ApiController
{
    /**
     * Get information about logged user
     * @return \Dingo\Api\Http\Response
     */
    public function me()
    {
        $doctor = $this->user()->doctor;
        if (!$doctor) {
            \Log::warning('User has role doctor but has not an assigned doctor', ['user' => ['id' => $this->user()->id, 'name' => $this->user()->name]]);
            $this->response->errorNotFound('User is not a doctor');
        }

        return $this->response->item(
            $this->user()->doctor,
            new DoctorProfileTransformer()
        );
    }

    /**
     * Change language for the logged user
     */
    public function lang($lang)
    {
        $user = $this->user();
        $user->lang = $lang;
        $user->save();
        return $this->response->noContent();
    }
}
