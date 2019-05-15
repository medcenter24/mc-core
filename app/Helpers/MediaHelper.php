<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

namespace medcenter24\mcCore\App\Helpers;



use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use Spatie\MediaLibrary\HasMedia\HasMedia;

class MediaHelper
{
    /**
     * @param HasMedia $model
     * @param string $collectionName
     * @param string $thumbName
     * @return string
     * @throws InconsistentDataException
     */
    public static function b64(HasMedia $model, $collectionName = '', $thumbName = 'thumb'): string
    {
        if (!$model->hasMedia($collectionName)) {
            throw new InconsistentDataException('Model does not have medias');
        }

        $path = $model->getFirstMediaPath($collectionName, $thumbName);
        return file_exists($path) ? base64_encode(file_get_contents($path)) : 'noContent';
    }

    /**
     * If no image available
     * @return string
     */
    public static function getB64Gag(): string
    {
        return base64_encode(file_get_contents(resource_path('assets/img/no_image_available.jpg')));
    }
}
