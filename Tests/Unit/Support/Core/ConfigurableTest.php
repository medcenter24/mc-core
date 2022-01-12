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

declare(strict_types=1);

namespace medcenter24\mcCore\Tests\Unit\Support\Core;

use medcenter24\mcCore\App\Exceptions\CommonException;
use medcenter24\mcCore\Tests\TestCase;

class ConfigurableTest extends TestCase
{
    public function testConstructorNoConfig(): void
    {
        $configTest = new ConfigTest();
        $this->assertEquals([], $configTest->getOptions());
    }

    /**
     * @throws CommonException
     */
    public function testConstructorWithObject(): void
    {
        $configTest = new ConfigTest(new MyConfigObject());
        // the default options should be merged with the constructor values,
        // overwriting any default values.
        $expectedOptions = array(
            'option2' => 'newValue2',
            'option3' => 3,
        );
        $this->assertEquals($expectedOptions, $configTest->getOptions());
    }

    /**
     * @throws CommonException
     */
    public function testConstructorWithArrayConfig(): void
    {
        $configTest = new ConfigTest(['option2' => 'newValue2', 'option3' => 3]);
        // the default options should be merged with the constructor values,
        // overwriting any default values.
        $expectedOptions = array(
            'option2' => 'newValue2',
            'option3' => 3,
        );
        $this->assertEquals($expectedOptions, $configTest->getOptions());
    }

    public function testSetOptionsException(): void
    {
        $this->expectException(CommonException::class);
        $this->expectExceptionMessage('Options submitted to medcenter24\mcCore\Tests\Unit\Support\Core\ConfigTest must be an array or implement toArray');
        $configTest = new ConfigTest();
        $configTest->setOptions('');
    }

    /**
     * @throws CommonException
     */
    public function testOverwriteOptions(): void
    {
        $configTest = new ConfigTest(['a']);
        $this->assertSame(['a'], $configTest->getOptions());
        $configTest->setOptions(['b']);
        $this->assertSame(['a', 'b'], $configTest->getOptions());
        $configTest->setOptions(['c'], true);
        $this->assertSame(['c'], $configTest->getOptions());
    }
}
