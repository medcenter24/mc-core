<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Support\Facades;


use App\Services\RoleService;
use Illuminate\Support\Facades\Facade;

class Roles extends Facade
{
    protected static function getFacadeAccessor()
    {
        return RoleService::class;
    }
}
