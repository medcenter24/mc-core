<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests;


trait SamplePath
{
    /**
     * Path to the folder with docx examples
     * @var string
     */
    private $samplePath = '';

    protected function getSamplePath()
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

    protected function getSampleFile($file='')
    {
        return $this->getSamplePath() . $file;
    }
}
