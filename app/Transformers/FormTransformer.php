<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\Form;
use League\Fractal\TransformerAbstract;

class FormTransformer extends TransformerAbstract
{
    public function transform(Form $form)
    {
        return [
            'id' => (int)$form->id,
            'title' => $form->title,
            'description' => $form->description,
            'type' => $form->formable_type,
            'variables' => $form->variables,
            'template' => $form->template,
            'formableType' => $form->formable_type,
        ];
    }
}
