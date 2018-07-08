<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Helpers;



use Spatie\MediaLibrary\HasMedia\Interfaces\HasMedia;

class MediaHelper
{
    /**
     * @param HasMedia $model
     * @param string $collectionName
     * @param string $thumbName
     * @return string
     * @throws \ErrorException
     */
    public static function b64(HasMedia $model, $collectionName = '', $thumbName = 'thumb')
    {
        if (!$model->hasMedia($collectionName)) {
            throw new \ErrorException('Model does not have medias');
        }

        $path = $model->getFirstMediaPath($collectionName, $thumbName);
        return file_exists($path) ? base64_encode(file_get_contents($path)) : 'noContent';
    }

    /**
     * If no image available
     * @return string
     */
    public static function getB64Gag()
    {
        return base64_encode(file_get_contents(resource_path('assets/img/no_image_available.jpg')));
    }
}
