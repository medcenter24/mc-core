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
    public function getScenarioByTag($tag = '')
    {
        return Scenario::where('tag', $tag)
            ->orderBy('order')
            ->get();
    }
}
