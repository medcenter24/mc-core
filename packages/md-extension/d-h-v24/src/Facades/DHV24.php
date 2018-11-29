<?php

namespace mdExtension\DHV24\Facades;

use Illuminate\Support\Facades\Facade;

class DHV24 extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'd-h-v24';
    }
}
