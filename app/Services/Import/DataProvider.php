<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\Import;


abstract class DataProvider
{
    private $data = false;

    /**
     * Load file to data provider
     *
     * @param string $path
     * @return DataProvider | $this
     */
    abstract public function load($path = '');

    /**
     * Check that file could be parsed by that DataProvider
     * @return bool
     */
    abstract public function check();

    /**
     * Load parsed data as array
     * @return array | false
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Store case (accident) to data base
     * @return bool
     */
    abstract public function import();
}
