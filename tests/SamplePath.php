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

namespace Tests;


trait SamplePath
{
    /**
     * Path to the folder with docx examples
     * @var string
     */
    private $samplePath = '';

    /**
     * @return string
     * @throws \ReflectionException
     */
    protected function getSamplePath(): string
    {
        if (!$this->samplePath) {
            $reflector = new \ReflectionClass(get_class($this));
            $this->samplePath = dirname($reflector->getFileName())
                . DIRECTORY_SEPARATOR
                . 'samples'
                . DIRECTORY_SEPARATOR;
        }

        return $this->samplePath;
    }

    protected function getTopAppSamplePath(): string
    {
        return __DIR__ . '/samples';
    }

    /**
     * @param string $file
     * @return string
     * @throws \ReflectionException
     */
    protected function getSampleFile($file=''): string
    {
        return $this->getSamplePath() . $file;
    }
}
