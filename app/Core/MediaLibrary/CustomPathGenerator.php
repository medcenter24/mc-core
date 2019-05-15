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

namespace medcenter24\mcCore\App\Core\MediaLibrary;


use Spatie\MediaLibrary\Models\Media;
use Spatie\MediaLibrary\PathGenerator\PathGenerator;
use Exception;

class CustomPathGenerator implements PathGenerator
{
    /**
     * @param Media $media
     * @return string
     * @throws Exception
     */
    public function getPath(Media $media) : string
    {
        $pass = md5($media->getAttribute('id') . $media->getAttribute('created_at'));
        $dir = substr($pass, 0, 2) . '/' . substr($pass, 2, 2);
        return $dir.'/';
    }

    /**
     * @param Media $media
     * @return string
     * @throws Exception
     */
    public function getPathForConversions(Media $media) : string
    {
        return $this->getPath($media).'c/';
    }

    /**
     * @param Media $media
     * @return string
     * @throws Exception
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media).'/cri/';
    }
}
