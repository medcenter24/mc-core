<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksandr Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Http\Requests\Api;


class CaseRequest extends JsonRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'accident' => [
                function ($attrName, $accidentValues, $failFunc) {
                    if (!is_array($accidentValues)) {
                        $failFunc($attrName . ' has to be an array');
                    }
                    $accidentRequest = new AccidentRequest();
                    $validator = validator($accidentValues, $accidentRequest->rules());

                    if ($validator->fails()) {
                        $failFunc($validator->errors()->toJson());
                    }
                }
            ],
        ];
    }
}
