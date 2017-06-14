<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Helpers\MediaLibrary;


use Spatie\MediaLibrary\Media;
use Spatie\MediaLibrary\PathGenerator\PathGenerator;

class DocPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        return sprintf("%02x" . DIRECTORY_SEPARATOR . "%02x", mt_rand(0, 255), mt_rand(0, 255))
            . DIRECTORY_SEPARATOR
            . mt_rand(1111, 9999) . '_' . $media->id;
    }

    public function getPathForConversions(Media $media) : string
    {
        $path = $media->getPath();
        $path = substr($path, 0, strrpos($path, '.'));
        return $path;
    }
}
