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

namespace Tests\samples;


trait SamplesTrait
{
    /**
     * Path to the samples folder
     * @return string
     */
    protected function getSamplesPath()
    {
        return __DIR__ . DIRECTORY_SEPARATOR;
    }

    /**
     * Getting file path from the samples
     * @param $path
     * @return string
     */
    protected function getSampleFilePath($path)
    {
        return $this->getSamplesPath() . $path;
    }

    /**
     * Loading content of the file from the samples
     * @param $path
     * @return bool|string
     */
    protected function getSampleFileContent($path)
    {
        return file_get_contents($this->getSampleFilePath($path));
    }

    /**
     * Get json content by the file path
     * @param $path
     * @return mixed
     */
    protected function getSampleJson($path)
    {
        return json_decode($this->getSampleFileContent($path), true);
    }
}
