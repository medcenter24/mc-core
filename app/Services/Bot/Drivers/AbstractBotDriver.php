<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\Bot\Drivers;


use App\Services\Bot\Bot;

abstract class AbstractBotDriver implements Bot
{
    /**
     * @var array
     */
    private $configuration;

    public function __construct(array $conf = [])
    {
        $this->configuration = $conf;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getConfiguration()
    {
        return collect($this->configuration);
    }

}