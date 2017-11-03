<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Scenario;

use App\AccidentStatus;
use App\Services\AccidentStatusesService;
use App\Services\Scenario\DoctorScenarioService;
use App\Services\Scenario\ScenarioService;
use Illuminate\Support\Collection;
use Prophecy\Argument;
use Tests\TestCase;

class DoctorScenarioServiceTest extends TestCase
{
    /**
     * @var DoctorScenarioService
     */
    private $service;

    public function setUp()
    {
        parent::setUp();

        $accidentStatusService = $this->prophesize(AccidentStatusesService::class);
        $scenarioService = $this->prophesize(ScenarioService::class);
        $this->service = new DoctorScenarioService($accidentStatusService->reveal(), $scenarioService->reveal());
        $this->service->setScenario(new Collection(\ScenariosTableSeeder::DOCTOR_SCENARIO));
    }

    public function testSetCurrentStepId()
    {
        self::assertCount(7, $this->service->scenario(), 'Doctor case scenario has 6 steps');
        self::assertNull($this->service->current(), 'Current step is not defined');
        $this->service->setCurrentStepId(1);
        self::assertEquals(1, $this->service->current(), 'Current step has been set by step id.');
        $this->service->setCurrentStepId(3);
        self::assertEquals(3, $this->service->current(), 'Current step has been set by step id.');
    }

    public function testMove()
    {
        $this->service->setCurrentStepId(1);
        self::assertEquals(1, $this->service->current(), 'Current step has been set by step id.');
        $this->service->next();
        self::assertEquals(2, $this->service->current(), 'Current step has been moved to the next step.');
        $this->service->setCurrentStepId(7);
        self::assertEquals(7, $this->service->current(), 'Current step has been set to the last one.');
        $this->service->next();
        self::assertEquals(7, $this->service->current(), 'Current step stood on the last step.');
    }
}
