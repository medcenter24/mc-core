<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Transformers;


use App\Upload;
use League\Fractal\TransformerAbstract;

class UploadedFileTransformer extends TransformerAbstract
{
    /**
     * @param Upload $file
     * @return array
     */
    public function transform (Upload $file)
    {
        return [
            'id'   => $file->id,
            'path' => $file->path,
            'name' => $file->file_name,
        ];
    }
}
