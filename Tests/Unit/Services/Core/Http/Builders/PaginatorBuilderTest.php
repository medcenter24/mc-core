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

use medcenter24\mcCore\App\Services\Core\Http\Builders\Paginator;
use medcenter24\mcCore\Tests\TestCase;

class PaginatorBuilderTest extends TestCase
{
    public function testDefaults(): void {
        $paginator = new Paginator();
        $paginator->inject([]);
        $this->assertSame(0, $paginator->getOffset());
        $this->assertSame(25, $paginator->getLimit());
    }

    public function testDefined(): void {
        $paginator = new Paginator();
        $paginator->inject([
            'fields' => [
                ['field' => 'offset', 'value' => 10],
                ['field' => 'limit', 'value' => 20],
            ]
        ]);

        $this->assertSame(10, $paginator->getOffset());
        $this->assertSame(20, $paginator->getLimit());
    }
}