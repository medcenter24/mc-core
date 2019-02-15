<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\Scenario;


use App\Scenario;

class ScenarioService
{

    /**
     * Don't need to load it twice
     * @var array
     */
    private $cache = [];

    public function getScenarioByTag($tag = '')
    {
        if (!key_exists($tag, $this->cache)) {
            $this->cache[$tag] = Scenario::where('tag', $tag)
                ->orderBy('order')
                ->get();
        }
        return $this->cache[$tag];
    }
}
