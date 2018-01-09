<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\CaseServices;


use App\Accident;
use Illuminate\Support\Collection;

class CaseHistoryService
{
    /**
     * @var Accident
     */
    private $accident;

    /**
     * @var Collection History
     */
    private $history;

    public function generate(Accident $accident)
    {
        $this->accident = $accident;
        $this->history = $accident->history()->orderBy('created_at')->get();
        return $this;
    }

    public function toArray()
    {
        return $this->history->toArray();
    }
}
