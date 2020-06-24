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

namespace medcenter24\mcCore\Tests\Unit\Services\Formula;

use medcenter24\mcCore\App\Models\Formula\FormulaBuilder;
use medcenter24\mcCore\App\Services\Formula\FormulaResultService;

class FormulaResultServiceTest extends AbstractDataProvider
{
    /**
     * @var FormulaResultService
     */
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = new FormulaResultService();
    }

    /**
     * @dataProvider dataProviders
     * @param FormulaBuilder $builder
     * @param string $view
     * @param int $expectedResult
     * @param string $description
     * @throws \Throwable
     */
    public function testResult(FormulaBuilder $builder, $view = '', $expectedResult = 0, $description = ''): void
    {
        self::assertEquals($expectedResult, $this->service->calculate($builder), $description);
    }
}
