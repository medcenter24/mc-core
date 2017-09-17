<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Support\Core;

use App\Support\Core\Configurable;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ConfigurableTest extends TestCase
{
    public function testConstructorNoConfig()
    {
        $configTest = new ConfigTest;
        $this->assertEquals($configTest->getOptions(), []);
    }

    public function testConstructorWithObject()
    {
        $configTest = new ConfigTest(new MyConfigObject);
        // the default options should be merged with the constructor values,
        // overwriting any default values.
        $expectedOptions = array(
            'option2' => 'newvalue2',
            'option3' => 3,
        );
        $this->assertEquals($expectedOptions, $configTest->getOptions());
    }

    public function testConstructorWithArrayConfig()
    {
        $configTest = new ConfigTest(
            array('option2' => 'newvalue2', 'option3' => 3)
        );
        // the default options should be merged with the constructor values,
        // overwriting any default values.
        $expectedOptions = array(
            'option2' => 'newvalue2',
            'option3' => 3,
        );
        $this->assertEquals($expectedOptions, $configTest->getOptions());
    }
}

class ConfigTest extends Configurable
{
    protected $options = array(
        'option1' => 1,
        'option2' => 'value 2',
    );
}

class MyConfigObject
{
    public function toArray()
    {
        return array('option2' => 'newvalue2', 'option3' => 3);
    }
}
