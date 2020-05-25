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

declare(strict_types = 1);

namespace medcenter24\mcCore\Tests\Feature\Api\Director\Cases;

use medcenter24\mcCore\App\Services\Entity\CaseAccidentService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\TestCase;

class CaseFinanceControllerTest extends TestCase
{
    use DirectorTestTraitApi;

    /**
     * @var CaseAccidentService
     */
    private $caseAccidentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->caseAccidentService = new CaseAccidentService();
    }

    public function testCaseFinances(): void
    {
        $accident = $this->caseAccidentService->create();
        $r1 = $this->sendPut('/api/director/cases/' . $accident->id . '/finance/income',
                [
                    'fixed' => true,
                    'price' => 101.99,
                ]
            );

        $r1->assertStatus(200);
        $r1->assertJson(
            array (
                'data' =>
                    array (
                        0 =>
                            array (
                                'type' => 'income',
                                'loading' => false,
                                'payment' =>
                                    array (
                                        'id' => 1,
                                        'createdBy' => 1,
                                        'value' => 101.99,
                                        'currencyId' => 1,
                                        'fixed' => true,
                                        'description' => 'Created from CaseFinanceService',
                                    ),
                                'currency' =>
                                    array (
                                        'id' => 1,
                                        'title' => 'Euro',
                                        'code' => 'eu',
                                        'ico' => 'fa fa-euro',
                                    ),
                                'formula' => 'fixed',
                                'calculatedValue' => 0,
                            ),
                        1 =>
                            array (
                                'type' => 'assistant',
                                'loading' => false,
                                'payment' => NULL,
                                'currency' =>
                                    array (
                                        'id' => 1,
                                        'title' => 'Euro',
                                        'code' => 'eu',
                                        'ico' => 'fa fa-euro',
                                    ),
                                'formula' => '0.00',
                                'calculatedValue' => 0,
                            ),
                        2 =>
                            array (
                                'type' => 'caseable',
                                'loading' => false,
                                'payment' => NULL,
                                'currency' =>
                                    array (
                                        'id' => 1,
                                        'title' => 'Euro',
                                        'code' => 'eu',
                                        'ico' => 'fa fa-euro',
                                    ),
                                'formula' => '0.00',
                                'calculatedValue' => 0,
                            ),
                    ),
            )
        );

        $r2 = $this->sendPut('/api/director/cases/' . $accident->id . '/finance/assistant',
            [
                'fixed' => false,
                'price' => 1.87,
            ]
        );

        $r2->assertStatus(200);
        $r2->assertJson(array (
            'data' =>
                array (
                    0 =>
                        array (
                            'type' => 'income',
                            'loading' => false,
                            'payment' =>
                                array (
                                    'id' => 1,
                                    'createdBy' => 1,
                                    'value' => 101.99,
                                    'currencyId' => 1,
                                    'fixed' => true,
                                    'description' => 'Created from CaseFinanceService',
                                ),
                            'currency' =>
                                array (
                                    'id' => 1,
                                    'title' => 'Euro',
                                    'code' => 'eu',
                                    'ico' => 'fa fa-euro',
                                ),
                            'formula' => 'fixed',
                            'calculatedValue' => 0,
                        ),
                    1 =>
                        array (
                            'type' => 'assistant',
                            'loading' => false,
                            'payment' =>
                                array (
                                    'id' => 2,
                                    'createdBy' => 1,
                                    'value' => 1.87,
                                    'currencyId' => 1,
                                    'fixed' => false,
                                    'description' => 'Created from CaseFinanceService',
                                ),
                            'currency' =>
                                array (
                                    'id' => 1,
                                    'title' => 'Euro',
                                    'code' => 'eu',
                                    'ico' => 'fa fa-euro',
                                ),
                            'formula' => '0.00',
                            'calculatedValue' => 0,
                        ),
                    2 =>
                        array (
                            'type' => 'caseable',
                            'loading' => false,
                            'payment' => NULL,
                            'currency' =>
                                array (
                                    'id' => 1,
                                    'title' => 'Euro',
                                    'code' => 'eu',
                                    'ico' => 'fa fa-euro',
                                ),
                            'formula' => '0.00',
                            'calculatedValue' => 0,
                        ),
                ),
        ));

        $r3 = $this->sendPut(
            '/api/director/cases/' . $accident->id . '/finance/caseable',
            [
                'fixed' => true,
                'price' => 0.99,
            ]
        );

        $r3->assertStatus(200);
        $r3->assertJson(array (
            'data' =>
                array (
                    0 =>
                        array (
                            'type' => 'income',
                            'loading' => false,
                            'payment' =>
                                array (
                                    'id' => 1,
                                    'createdBy' => 1,
                                    'value' => 101.99,
                                    'currencyId' => 1,
                                    'fixed' => true,
                                    'description' => 'Created from CaseFinanceService',
                                ),
                            'currency' =>
                                array (
                                    'id' => 1,
                                    'title' => 'Euro',
                                    'code' => 'eu',
                                    'ico' => 'fa fa-euro',
                                ),
                            'formula' => 'fixed',
                            'calculatedValue' => 0,
                        ),
                    1 =>
                        array (
                            'type' => 'assistant',
                            'loading' => false,
                            'payment' =>
                                array (
                                    'id' => 2,
                                    'createdBy' => 1,
                                    'value' => 1.87,
                                    'currencyId' => 1,
                                    'fixed' => false,
                                    'description' => 'Created from CaseFinanceService',
                                ),
                            'currency' =>
                                array (
                                    'id' => 1,
                                    'title' => 'Euro',
                                    'code' => 'eu',
                                    'ico' => 'fa fa-euro',
                                ),
                            'formula' => '0.00',
                            'calculatedValue' => 0,
                        ),
                    2 =>
                        array (
                            'type' => 'caseable',
                            'loading' => false,
                            'payment' =>
                                array (
                                    'id' => 3,
                                    'createdBy' => 1,
                                    'value' => 0.99,
                                    'currencyId' => 1,
                                    'fixed' => true,
                                    'description' => 'Created from CaseFinanceService',
                                ),
                            'currency' =>
                                array (
                                    'id' => 1,
                                    'title' => 'Euro',
                                    'code' => 'eu',
                                    'ico' => 'fa fa-euro',
                                ),
                            'formula' => 'fixed',
                            'calculatedValue' => 0,
                        ),
                ),
        ));
    }
}
