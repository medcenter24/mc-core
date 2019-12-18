<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
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

use medcenter24\mcCore\App\Helpers\FileHelper;
use medcenter24\mcCore\App\Helpers\StrHelper;
use medcenter24\mcCore\Tests\TestCase;

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
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

class StrHelperTest extends TestCase
{
    public function getStrings(): array
    {
        return [
            [
                'str' => 'name',
                'expected' => 'name'
            ],
            [
                'str' => 'name another',
                'expected' => 'nameanother'
            ],
            [
                'str' => 'name/Another',
                'expected' => 'nameAnother'
            ],
            [
                'str' => 'name another.phpO0r1!2,',
                'expected' => 'nameanotherphpOr',
            ]
        ];
    }

    /**
     * @param string $name
     * @param string $expected
     * @dataProvider getStrings
     */
    public function testGetLetters(string $name, string $expected): void
    {
        $this->assertSame($expected, StrHelper::getLetters($name));
    }
}
