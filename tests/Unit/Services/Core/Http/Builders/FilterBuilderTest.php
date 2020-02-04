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

namespace medcenter24\mcCore\Tests\Unit\Services\Core\Http;


use medcenter24\mcCore\App\Services\Core\Http\Builders\Filter;
use medcenter24\mcCore\App\Services\Core\Http\Builders\Sorter;
use medcenter24\mcCore\Tests\TestCase;

class FilterBuilderTest extends TestCase
{
    public function testDefaults(): void {
        $filter= new Filter();
        $this->assertEquals(collect(), $filter->getFilters());
    }

    public function testDefaults2(): void {
        $filter= new Filter();
        $filter->inject([]);
        $this->assertEquals(collect(), $filter->getFilters());
    }

    public function testDefined(): void {
        $filter = new Filter();
        $filter->inject([
            'fields' => [
                ['field' => 'field1', 'value' => '1', 'match' => 'like', 'elType' => ''], // skip
                ['field' => 'field1', 'value' => '', 'match' => 'like', 'elType' => 'text'], // skip
                ['field' => 'field2', 'value' => '2', 'match' => 'like', 'elType' => 'text'],
                ['field' => 'field3', 'value' => '3', 'match' => 'in', 'elType' => 'select'],
                ['field' => 'field4', 'value' => 'aaa', 'match' => 'eq', 'elType' => 'dateRange'], // skip
                ['field' => 'field5', 'value' => '2018-10-02', 'match' => 'eq', 'elType' => 'dateRange'],
                ['field' => 'field6', 'value' => '2018-10-02>2019-02-10', 'match' => 'eq', 'elType' => 'dateRange'],
                ['field' => 'field7', 'value' => '2018-10-02  - 2019-02-10', 'match' => 'in', 'elType' => 'dateRange'], // skip
                ['field' => 'field8', 'value' => '8', 'match' => 'gt', 'elType' => 'text'],
            ]
        ]);

        $this->assertSame([
            // keys saved as well
            2 => ['field' => 'field2', 'value' => '2', 'match' => 'like', 'elType' => 'text'],
            3 => ['field' => 'field3', 'value' => '3', 'match' => 'in', 'elType' => 'select'],
            5 => ['field' => 'field5', 'value' => '2018-10-02', 'match' => 'eq', 'elType' => 'dateRange'],
            6 => ['field' => 'field6', 'value' => '2018-10-02>2019-02-10', 'match' => 'eq', 'elType' => 'dateRange'],
            8 => ['field' => 'field8', 'value' => '8', 'match' => 'gt', 'elType' => 'text'],
        ], $filter->getFilters()->toArray());
    }
}