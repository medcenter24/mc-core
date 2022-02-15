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
use Spatie\MediaLibrary\HasMedia;

class MediaHelper
{
    public const PNG_B64_PREFIX = 'data:image/png;base64,';
    public const JPG_B64_PREFIX = 'data:image/jpg;base64,';
    public const JPEG_B64_PREFIX = 'data:image/jpeg;base64,';

    /**
     * @param HasMedia $model
     * @param string $collectionName
     * @param string $thumbName
     * @return string
     * @throws InconsistentDataException
     */
    public static function b64(HasMedia $model, string $collectionName = '', string $thumbName = 'thumb'): string
    {
        if (!$model->hasMedia($collectionName)) {
            throw new InconsistentDataException('Model does not have medias');
        }
        $path = $model->getFirstMediaPath($collectionName, $thumbName);
        if (!file_exists($path)) {
            return '';
        }
        $b64 = base64_encode(file_get_contents($path));
        return self::detectB64Prefix($path) . $b64;
    }

    /**
     * @param string $path
     * @return string
     */
    public static function detectB64Prefix(string $path): string
    {
        return match (FileHelper::getExtension($path)) {
            'jpeg' => self::JPEG_B64_PREFIX,
            'png' => self::PNG_B64_PREFIX,
            default => self::JPG_B64_PREFIX,
        };
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
