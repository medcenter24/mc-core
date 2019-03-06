<?php
/**
 * Copyright (c) 2019.
 *
 * Oleksandr Zagovorychev <zagovorichev@gmail.com>
 */

namespace App\Services\CaseServices;


use App\Accident;
use Illuminate\Support\Collection;

class CaseSeekerService
{
    public function search(array $filters = []): Collection
    {
        return Accident::all();
    }
}
