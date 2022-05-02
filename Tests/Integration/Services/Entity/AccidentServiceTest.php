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

declare(strict_types=1);

namespace medcenter24\mcCore\Tests\Integration\Services\Entity;

use Database\Factories\Entity\ServiceFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\AccidentStatus;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Entity\Service;
use medcenter24\mcCore\App\Events\Accident\Status\AccidentStatusChangedEvent;
use medcenter24\mcCore\App\Services\Entity\AccidentService;
use medcenter24\mcCore\Tests\TestCase;

class AccidentServiceTest extends TestCase
{
    use DatabaseMigrations;

    private AccidentService $accidentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->accidentService = new AccidentService();
    }

    public function testEmptyCreate(): void
    {
        $accident = $this->accidentService->create();

        $this->assertSame([
            'id' => 1,
            'parent_id' => 0,
            'patient_id' => 0,
            'accident_type_id' => 0,
            'accident_status_id' => 1,
            'assistant_id' => 0,
            'assistant_ref_num' => '',
            'assistant_invoice_id' => 0,
            'assistant_guarantee_id' => 0,
            'form_report_id' => 0,
            'city_id' => 0,
            'ref_num' => '',
            'title' => '',
            'address' => '',
            'handling_time' => null,
            'contacts' => '',
            'symptoms' => '',
        ], $accident->attributesToArray());

        $this->assertSame(DoctorAccident::class, $accident->caseable_type);
    }

    public function testGetCountByReferralNum(): void
    {
        Accident::factory()->create([AccidentService::FIELD_REF_NUM => 'refNum']);
        Accident::factory()->create([AccidentService::FIELD_REF_NUM => 'refNum2']);

        $this->assertSame(1, $this->accidentService->getCountByReferralNum('refNum'));
    }

    public function testGetByAssistantRefNum(): void
    {
        $accident = Accident::factory()->create([AccidentService::FIELD_ASSISTANT_REF_NUM => 'refNum']);
        Accident::factory()->create([AccidentService::FIELD_ASSISTANT_REF_NUM => 'refNum2']);

        $this->assertSame($accident->id, $this->accidentService->getByAssistantRefNum('refNum')->id);
    }

    public function testGetCountByAssistant(): void
    {
        $dateBefore = now();
        Accident::factory()->create([AccidentService::FIELD_ASSISTANT_ID => 1]);
        Accident::factory()->create([AccidentService::FIELD_ASSISTANT_ID => 1]);
        Accident::factory()->create([AccidentService::FIELD_ASSISTANT_ID => 2]);

        $this->assertSame(2, $this->accidentService->getCountByAssistance(1, $dateBefore));
    }

    public function testGetAccidentServices(): void
    {
        /** @var Accident $accident */
        $accident = Accident::factory()->create([
            AccidentService::FIELD_CASEABLE_TYPE => DoctorAccident::class,
            AccidentService::FIELD_CASEABLE_ID => DoctorAccident::factory()->create()->getAttribute('id'),
        ]);
        $accident->caseable->services()->attach(
            Service::factory()->count(3)->create()
        );
        $this->assertCount(3, $this->accidentService->getAccidentServices($accident));
    }

    public function testSetStatusOnCreate(): void
    {
        Event::fake();
        /** @var Accident $accident */
        $accident = Accident::factory()->create([
            AccidentService::FIELD_ACCIDENT_STATUS_ID => 0
        ]);

        /** @var AccidentStatus $status */
        $status = AccidentStatus::factory()->create();

        $this->accidentService->setStatus($accident, $status, $commentary = 'PHPUnit');

        Event::assertDispatched(AccidentStatusChangedEvent::class, static function ($e) use($accident, $commentary) {
            return $e->getAccident()->id === $accident->id && $e->getCommentary() === $commentary;
        });

        $this->assertSame((int)$status->id, (int)$accident->getAttribute(AccidentService::FIELD_ACCIDENT_STATUS_ID));
    }
}
