<?php
/**
 * Copyright (c) 2018.
 *
 * @author Oleksander Zagovorychev <zagovorichev@gmail.com>
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
