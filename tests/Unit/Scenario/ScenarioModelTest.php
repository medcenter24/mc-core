<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Scenario;

use App\Models\Scenario\ScenarioModel;
use App\Services\AccidentStatusesService;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ScenarioModelTest extends TestCase
{
    /**
     * @var ScenarioModel
     */
    private $scenarioModel;

    public function setUp()
    {
        parent::setUp();

        $accidentStatusServiceMock = $this->prophesize(AccidentStatusesService::class);
        /** @var AccidentStatusesService $accidentStatusService */
        $accidentStatusService = $accidentStatusServiceMock->reveal();
        $this->scenarioModel = new ScenarioModel($accidentStatusService, new Collection(\ScenariosTableSeeder::DOCTOR_SCENARIO));
    }

    /**
     * @throws \App\Exceptions\InconsistentDataException
     */
    public function testSetCurrentStepId()
    {
        self::assertCount(7, $this->scenarioModel->scenario(), 'Doctor case scenario has 6 steps');
        self::assertNull($this->scenarioModel->current(), 'Current step is not defined');
        $this->scenarioModel->setCurrentStepId(1);
        self::assertEquals(1, $this->scenarioModel->current(), 'Current step has been set by step id.');
        $this->scenarioModel->setCurrentStepId(3);
        self::assertEquals(3, $this->scenarioModel->current(), 'Current step has been set by step id.');
    }

    /**
     * @throws \App\Exceptions\InconsistentDataException
     */
    public function testMove()
    {
        $this->scenarioModel->setCurrentStepId(1);
        self::assertEquals(1, $this->scenarioModel->current(), 'Current step has been set by step id.');
        $this->scenarioModel->next();
        self::assertEquals(2, $this->scenarioModel->current(), 'Current step has been moved to the next step.');
        $this->scenarioModel->setCurrentStepId(7);
        self::assertEquals(7, $this->scenarioModel->current(), 'Current step has been set to the last one.');
        $this->scenarioModel->next();
        self::assertEquals(7, $this->scenarioModel->current(), 'Current step stood on the last step.');
    }
}
