<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Scenario;

use App\AccidentStatus;
use App\AccidentStatusHistory;
use App\DoctorAccident;
use App\Services\Scenario\DoctorScenarioService;
use App\Services\Scenario\StoryService;
use Illuminate\Support\Collection;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StoryServiceTest extends TestCase
{

    use DatabaseMigrations;

    private function getDoctorScenario()
    {
        $scenario = new Collection();
        foreach (\ScenariosTableSeeder::DOCTOR_SCENARIO as $row) {
            $scenario->push(array_merge($row, ['accident_status_id' => AccidentStatus::firstOrCreate([
                    'type' => $row['type'],
                    'title' => $row['title'],
                ])->id,
                'tag' => DoctorAccident::class,
                'mode' => array_key_exists('mode', $row) ? $row['mode'] : \ScenariosTableSeeder::DEFAULT_MODE,
            ]));
        }

        return $scenario;
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCasualStory()
    {
        $doctorScenarioService = new DoctorScenarioService();
        $doctorScenarioService->setScenario($this->getDoctorScenario());

        $history = new Collection(
            [
                factory(AccidentStatusHistory::class)->create([
                    'accident_status_id' => AccidentStatus::firstOrCreate([
                            'type' => \AccidentStatusesTableSeeder::TYPE_ACCIDENT,
                            'title' => \AccidentStatusesTableSeeder::STATUS_NEW,
                        ])->id
                ]),
                factory(AccidentStatusHistory::class)->create([
                    'accident_status_id' => AccidentStatus::firstOrCreate([
                            'type' => \AccidentStatusesTableSeeder::TYPE_DOCTOR,
                            'title' => \AccidentStatusesTableSeeder::STATUS_ASSIGNED,
                        ])->id
                ]),
            ]
        );

        $storyService = new StoryService();
        $story = $storyService->init($history, $doctorScenarioService)->getStory();
        self::assertEquals(6, $story->count(), 'Story contents 6 steps');
        self::assertEquals('{"0":{"order":1,"title":"new","type":"accident","accident_status_id":1,"tag":"App\\\\DoctorAccident","mode":"step","status":"visited"},"1":{"order":2,"title":"assigned","type":"doctor","accident_status_id":2,"tag":"App\\\\DoctorAccident","mode":"step","status":"visited"},"2":{"order":3,"title":"in_progress","type":"doctor","accident_status_id":3,"tag":"App\\\\DoctorAccident","mode":"step"},"3":{"order":4,"title":"sent","type":"doctor","accident_status_id":4,"tag":"App\\\\DoctorAccident","mode":"step"},"4":{"order":5,"title":"paid","type":"doctor","accident_status_id":5,"tag":"App\\\\DoctorAccident","mode":"step"},"6":{"order":7,"title":"closed","type":"accident","accident_status_id":7,"tag":"App\\\\DoctorAccident","mode":"step"}}',
            $story->toJson(), 'Expected story was loaded');
    }

    /**
     * Add skip on the second step
     */
    public function testRejectedStory()
    {
        $doctorScenarioService = new DoctorScenarioService();
        $doctorScenarioService->setScenario($this->getDoctorScenario());

        $history = new Collection(
            [
                factory(AccidentStatusHistory::class)->create([
                    'accident_status_id' => AccidentStatus::firstOrCreate([
                        'type' => \AccidentStatusesTableSeeder::TYPE_ACCIDENT,
                        'title' => \AccidentStatusesTableSeeder::STATUS_NEW,
                    ])->id
                ]),
                factory(AccidentStatusHistory::class)->create([
                    'accident_status_id' => AccidentStatus::firstOrCreate([
                        'type' => \AccidentStatusesTableSeeder::TYPE_DOCTOR,
                        'title' => \AccidentStatusesTableSeeder::STATUS_ASSIGNED,
                    ])->id
                ]),
                factory(AccidentStatusHistory::class)->create([
                    'accident_status_id' => AccidentStatus::firstOrCreate([
                        'type' => \AccidentStatusesTableSeeder::TYPE_DOCTOR,
                        'title' => \AccidentStatusesTableSeeder::STATUS_REJECT,
                    ])->id
                ]),
            ]
        );

        $storyService = new StoryService();
        $story = $storyService->init($history, $doctorScenarioService)->getStory();
        self::assertEquals(4, $story->count(), 'Story contents 6 steps');
        self::assertEquals('{"0":{"order":1,"title":"new","type":"accident","accident_status_id":1,"tag":"App\\\\DoctorAccident","mode":"step","status":"visited"},"1":{"order":2,"title":"assigned","type":"doctor","accident_status_id":2,"tag":"App\\\\DoctorAccident","mode":"step","status":"visited"},"5":{"order":6,"title":"reject","type":"doctor","mode":"skip:doctor","accident_status_id":6,"tag":"App\\\\DoctorAccident","status":"visited"},"6":{"order":7,"title":"closed","type":"accident","accident_status_id":7,"tag":"App\\\\DoctorAccident","mode":"step"}}',
            $story->toJson(), 'Expected story was loaded');
    }
}
