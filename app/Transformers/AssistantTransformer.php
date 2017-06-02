<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\Assistant;
use League\Fractal\TransformerAbstract;

class AssistantTransformer extends TransformerAbstract
{
    public function transform(Assistant $assistant)
    {
        return [
            'id' => $assistant->id,
            'title' => $assistant->title,
            'email' => $assistant->email,
            'comment' => $assistant->comment,
            'ref_key' => $assistant->ref_key,
            'picture' => 'pic_path'
        ];
    }
}
