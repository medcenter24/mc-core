<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\Diagnostic;
use League\Fractal\TransformerAbstract;

class DiagnosticTransformer extends TransformerAbstract
{
    public function transform(Diagnostic $diagnostic)
    {
        return [
            'id' => $diagnostic->id,
            'title' => $diagnostic->title,
            'description' => $diagnostic->description,
            'diagnostic_category_id' => $diagnostic->diagnostic_category_id,
            'disease_code' => $diagnostic->disease_code,
            'type' => $diagnostic->isDoctor() ? 'doctor' : ''
        ];
    }
}
