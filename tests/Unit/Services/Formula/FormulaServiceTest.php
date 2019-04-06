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

namespace Tests\Unit\Services\Formula;

use App\Contract\Formula\FormulaBuilder;
use App\FinanceCondition;
use App\Models\Formula\FormulaBuilder as FormulaBuilderModel;
use App\Services\Formula\FormulaService;
use Illuminate\Support\Collection;
use Tests\TestCase;

class FormulaServiceTest extends TestCase
{
    /**
     * @var FormulaService
     */
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = new FormulaService();
    }

    public function testCreateFormula()
    {
        self::assertInstanceOf(FormulaBuilder::class, $this->service->createFormula());
    }

    /**
     * Conditions for the test
     *
     * Currencies doesn't affect the result / convert of the currency should be done before this operation
     * in the CaseFinanceService (or somewhere higher)
     *
     * @return array
     * @throws \App\Models\Formula\Exception\FormulaException
     */
    public function dataProvider()
    {
        return [
            [ collect([]), new FormulaBuilderModel() ],
            [ collect([
                new FinanceCondition([
                    'value' => 10,
                    'type' => 'add',
                    'currency_id' => '1',
                    'currency_mode' => 'currency',
                ]),
            ]), (new FormulaBuilderModel())->addFloat(10) ],
            [ collect([
                new FinanceCondition([
                    'value' => 10,
                    'type' => 'add',
                    'currency_id' => '1',
                    'currency_mode' => 'currency',
                ]),
                new FinanceCondition([
                    'value' => 50,
                    'type' => 'sub',
                    'currency_id' => '0',
                    'currency_mode' => 'percent',
                ]),
            ]), (new FormulaBuilderModel())->addFloat(10)->subPercent(50) ],
            [ collect([
                new FinanceCondition([
                    'value' => 10,
                    'type' => 'add',
                    'currency_id' => '1',
                    'currency_mode' => 'currency',
                ]),
                new FinanceCondition([
                    'value' => 50,
                    'type' => 'add',
                    'currency_id' => '1',
                    'currency_mode' => 'currency',
                ]),
                new FinanceCondition([
                    'value' => 10,
                    'type' => 'sub',
                    'currency_id' => '1',
                    'currency_mode' => 'currency',
                ]),
                new FinanceCondition([
                    'value' => 50,
                    'type' => 'sub',
                    'currency_id' => '0',
                    'currency_mode' => 'percent',
                ]),
                new FinanceCondition([
                    'value' => 10,
                    'type' => 'add',
                    'currency_id' => '0',
                    'currency_mode' => 'percent',
                ]),
                new FinanceCondition([
                    'value' => 2,
                    'type' => 'mul',
                    'currency_id' => '0',
                    'currency_mode' => 'currency',
                ]),
                new FinanceCondition([
                    'value' => 4,
                    'type' => 'div',
                    'currency_id' => '0',
                    'currency_mode' => 'currency',
                ]),
            ]), (new FormulaBuilderModel())->addFloat(10)->addFloat(50)->subFloat(10)->subPercent(50)->addPercent(10)->mulFloat(2)->divFloat(4) ]
        ];
    }

    /**
     * @dataProvider dataProvider
     * @param Collection $collection
     * @param FormulaBuilderModel $formula
     */
    public function testCreateFormulaFromConditions(Collection $collection, FormulaBuilderModel $formula)
    {
        self::assertEquals($this->service->createFormulaFromConditions($collection), $formula);
    }
}
