<?php

use medcenter24\mcCore\App\Helpers\FileHelper;
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

class FileHelperTest extends TestCase
{
    public function getFileNames(): array
    {
        return [
            [
                'name' => 'name',
                'expected' => 'Name'
            ],
            [
                'name' => 'name another',
                'expected' => 'NameAnother'
            ],
            [
                'name' => 'name/Another',
                'expected' => 'NameAnother'
            ],
            [
                'name' => 'name another.phpOr1!2',
                'expected' => 'NameAnotherPhpOr12'
            ]
        ];
    }

    /**
     * @param string $name
     * @param string $expected
     * @dataProvider getFileNames
     */
    public function testPurifiedFileName(string $name, string $expected): void
    {
        $this->assertSame($expected, FileHelper::purifiedFileName($name));
    }
}
