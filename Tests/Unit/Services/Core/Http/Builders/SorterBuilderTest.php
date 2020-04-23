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
 * Copyright (c) 2020 (original work) MedCenter24.com;
 */

namespace medcenter24\mcCore\Tests\Unit\Services\Core\Http\Builders;


use medcenter24\mcCore\App\Services\Core\Http\Builders\Sorter;
use medcenter24\mcCore\Tests\TestCase;

class SorterBuilderTest extends TestCase
{
    public function testDefaults(): void {
        $sorter= new Sorter();
        $this->assertEquals(collect(), $sorter->getSortBy());
    }

    public function testDefaults2(): void {
        $sorter= new Sorter();
        $sorter->inject([]);
        $this->assertEquals(collect(), $sorter->getSortBy());
    }

    public function testDefined(): void {
        $sorter = new Sorter();
        $sorter->inject([
            'fields' => [
                ['field' => 'field1', 'value' => ''],
                ['field' => 'field2', 'value' => 'desc'],
                ['field' => 'field3', 'value' => 'asc'],
                ['field' => 'field4', 'value' => 'none'],
            ]
        ]);

        $this->assertSame([
            // we have to save order (keys) to sort it in order
            1 => ['field' => 'field2', 'value' => 'desc'],
            2 => ['field' => 'field3', 'value' => 'asc'],
        ], $sorter->getSortBy()->toArray());
    }
}