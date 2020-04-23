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

namespace medcenter24\mcCore\Tests\Feature\Api\Doctor\Accident;

use medcenter24\mcCore\Tests\TestCase;

class StatusAccidentControllerTest extends TestCase
{
    use TestDoctorAccidentTrait;

    public function testGet(): void
    {
        $accident = $this->createAccidentForDoc();
        $response = $this->sendGet('/api/doctor/accidents/'.$accident->id.'/status');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => 1,
                'hasParent' => false,
            ],
        ]);
    }

    public function testReject(): void
    {
        $accident = $this->createAccidentForDoc();
        $response = $this->sendPatch('/api/doctor/accidents/'.$accident->id.'/reject', []);
        $response->assertStatus(204);
        $accident->refresh();
        $this->assertSame('reject', $accident->accidentStatus->title);
    }

    public function testSend(): void
    {
        $accident = $this->createAccidentForDoc();
        $response = $this->sendPost('/api/doctor/accidents/send', [
            'cases' => [$accident->id],
        ]);
        $response->assertStatus(204);
        $accident->refresh();
        $this->assertSame('sent', $accident->accidentStatus->title);
    }
}
