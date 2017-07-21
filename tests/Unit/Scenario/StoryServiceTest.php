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
        self::assertEquals('', $story->toJson(), 'Expected story was loaded');
    }

    public function testRejectedStory()
    {

    }
}
