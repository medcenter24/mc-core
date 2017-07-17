<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Scenario;

use App\AccidentStatus;
use App\Services\Scenario\DoctorScenarioService;
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
        $this->service = new DoctorScenarioService();
    }

    public function testScenario()
    {
        self::assertCount(7, $this->service->scenario(), 'Doctor case scenario has 6 steps');
        self::assertNull($this->service->current(), 'Current step is not defined');
        $this->service->set(1);
        self::assertEquals(1, $this->service->current(), 'Current step has been set by step id.');
        $this->service->set([
            'title' => 'in_progress',
            'type'  => 'doctor',
        ]);
        self::assertEquals(3, $this->service->current(), 'Current step has been set by the array.');
        $status = new AccidentStatus([
            'title' => 'closed',
            'type' => 'accident',
        ]);
        $this->service->set($status);
        self::assertEquals(7, $this->service->current(), 'Current step has been set by the AccidentStatus object');
    }

    public function testMove()
    {
        $this->service->set(1);
        self::assertEquals(1, $this->service->current(), 'Current step has been set by step id.');
        $this->service->next();
        self::assertEquals(2, $this->service->current(), 'Current step has been moved to the next step.');
        $this->service->set(7);
        self::assertEquals(7, $this->service->current(), 'Current step has been set to the last one.');
        $this->service->next();
        self::assertEquals(7, $this->service->current(), 'Current step stood on the last step.');
    }
}
