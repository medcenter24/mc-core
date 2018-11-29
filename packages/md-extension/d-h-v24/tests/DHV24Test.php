<?php

namespace mdExtension\DHV24\Tests;

use mdExtension\DHV24\Facades\DHV24;
use mdExtension\DHV24\ServiceProvider;
use Orchestra\Testbench\TestCase;

class DHV24Test extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'd-h-v24' => DHV24::class,
        ];
    }

    public function testExample()
    {
        $this->assertEquals(1, 1);
    }
}
