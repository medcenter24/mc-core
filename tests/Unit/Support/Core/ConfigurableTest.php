<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

namespace medcenter24\mcCore\Tests\Unit\Support\Core;

use medcenter24\mcCore\App\Support\Core\Configurable;
use medcenter24\mcCore\Tests\TestCase;

class ConfigurableTest extends TestCase
{
    public function testConstructorNoConfig(): void
    {
        $configTest = new ConfigTest;
        $this->assertEquals($configTest->getOptions(), []);
    }

    public function testConstructorWithObject(): void
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

    public function testConstructorWithArrayConfig(): void
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
    public function toArray(): array
    {
        return array('option2' => 'newvalue2', 'option3' => 3);
    }
}
