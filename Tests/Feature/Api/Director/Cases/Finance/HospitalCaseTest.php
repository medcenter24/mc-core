<?php
/*
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
 * Copyright (c) 2022 (original work) MedCenter24.com;
 */

declare(strict_types=1);

namespace Api\Director\Cases\Finance;

use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\FinanceCurrency;
use medcenter24\mcCore\App\Entity\Hospital;
use medcenter24\mcCore\App\Entity\HospitalAccident;
use medcenter24\mcCore\App\Entity\Invoice;
use medcenter24\mcCore\App\Entity\Payment;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\App\Services\Entity\HospitalAccidentService;
use medcenter24\mcCore\App\Services\Entity\InvoiceService;
use medcenter24\mcCore\App\Services\Entity\PaymentService;
use medcenter24\mcCore\Tests\Feature\Api\DirectorTestTraitApi;
use medcenter24\mcCore\Tests\TestCase;

class HospitalCaseTest extends TestCase
{
    use DirectorTestTraitApi;

    public function testHospitalInvoicesWorkflow(): void
    {
        $hospital = Hospital::factory()->create();

        $currency = FinanceCurrency::factory()->create();

        $hospitalInvoicePayment = Payment::factory()
            ->create([
                PaymentService::FIELD_VALUE       => 10,
                PaymentService::FIELD_CURRENCY_ID => $currency->id,
            ]);
        $hospitalInvoice = Invoice::factory()->create([
            InvoiceService::FIELD_PAYMENT_ID => $hospitalInvoicePayment->id,
        ]);

        $caseable = HospitalAccident::factory()->create(
            [
                HospitalAccidentService::FIELD_HOSPITAL_ID         => $hospital->id,
                HospitalAccidentService::FIELD_HOSPITAL_INVOICE_ID => $hospitalInvoice->id,
            ]
        );

        $caseablePaymentId = Payment::factory()
            ->create([
                PaymentService::FIELD_VALUE       => 99.99,
                PaymentService::FIELD_FIXED       => true,
                PaymentService::FIELD_CURRENCY_ID => $currency->id,
            ]);

        $accident = Accident::factory()->create(
            [
                AccidentService::FIELD_CASEABLE_TYPE       => HospitalAccident::class,
                AccidentService::FIELD_CASEABLE_ID         => $caseable->id,
                AccidentService::FIELD_CASEABLE_PAYMENT_ID => $caseablePaymentId,
            ],
        );

        $response = $this->sendGet('/api/director/cases/' . $accident->id . '/finance?types=caseable');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    "type"            => "caseable",
                    "loading"         => false,
                    "payment"         => [
                        "id"          => 3,
                        "createdBy"   => 0,
                        "value"       => 99.99,
                        "currencyId"  => 1,
                        "fixed"       => true,
                        "description" => "faked payment",
                    ],
                    "currency"        => [
                        "id"    => 1,
                    ],
                    "formula"         => "fixed",
                    "calculatedValue" => 0,
                ],
            ],
        ]);
    }
}
