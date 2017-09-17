<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\DiagnosticCategory;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    /**
     * @param DiagnosticCategory $diagnosticCategory
     * @return array
     */
    public function transform (DiagnosticCategory $diagnosticCategory)
    {
        return [
            'id' => $diagnosticCategory->id,
            'title' => $diagnosticCategory->title
        ];
    }
}
