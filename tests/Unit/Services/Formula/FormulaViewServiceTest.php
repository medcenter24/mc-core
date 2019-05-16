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

use medcenter24\mcCore\App\Contract\Formula\FormulaBuilder;
use medcenter24\mcCore\App\Models\Formula\FormulaBuilder as FormulaBuilderModel;
use medcenter24\mcCore\App\Services\Formula\FormulaViewService;

class FormulaViewServiceTest extends AbstractDataProvider
{
    /**
     * @var FormulaViewService
     */
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = new FormulaViewService();
    }

    /**
     * @dataProvider dataProviders
     * @param FormulaBuilder $builder
     * @param string $view
     * @param int $expectedResult
     * @param string $description
     * @throws \Throwable
     */
    public function testView(FormulaBuilder $builder, $view = '', $expectedResult = 0, $description = ''): void
    {
        self::assertEquals($view, $this->service->render($builder), $description);
    }

    /**
     * @throws \medcenter24\mcCore\App\Models\Formula\Exception\FormulaException
     * @throws \Throwable
     * @expectedException \medcenter24\mcCore\App\Models\Formula\Exception\FormulaException
     * @expectedExceptionMessage Divide by zero
     */
    public function testDivideByZero(): void
    {
        $this->service->render( (new FormulaBuilderModel())->divInteger(0) );
    }

    /**
     * @throws \medcenter24\mcCore\App\Models\Formula\Exception\FormulaException
     * @throws \Throwable
     * @expectedException \medcenter24\mcCore\App\Models\Formula\Exception\FormulaException
     * @expectedExceptionMessage Divide by zero
     */
    public function testDivideByZero2(): void
    {
        $this->service->render( (new FormulaBuilderModel())->divInteger(1)->divInteger(0) );
    }
}
