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
declare(strict_types = 1);

namespace Database\Seeders;

use Illuminate\Support\Facades\App;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\AccidentCheckpoint;
use medcenter24\mcCore\App\Entity\AccidentStatus;
use medcenter24\mcCore\App\Entity\AccidentType;
use medcenter24\mcCore\App\Entity\Assistant;
use medcenter24\mcCore\App\Entity\City;
use medcenter24\mcCore\App\Entity\Diagnostic;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Entity\Service;
use medcenter24\mcCore\App\Entity\Survey;
use medcenter24\mcCore\App\Entity\Form;
use medcenter24\mcCore\App\Entity\FormReport;
use medcenter24\mcCore\App\Entity\Patient;
use medcenter24\mcCore\App\Entity\User;
use Illuminate\Database\Seeder;

class AccidentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment('production') && Accident::all()->count()) {
            return;
        } elseif (!App::environment('production')) {
            Accident::truncate();
            Accident::factory()->create(5)->create([
                'created_by' => User::factory()->create(),
                'patient_id' => Patient::factory()->create(),
                'accident_type_id' => function () {
                    return !AccidentType::count()
                        ? AccidentType::factory()->create()
                        : AccidentType::first();
                },
                'accident_status_id' => AccidentStatus::firstOrCreate(AccidentStatusesTableSeeder::ACCIDENT_STATUSES[0]), // new
                'assistant_id' => function () {
                    return Assistant::factory()->create();
                },
                'caseable_id' => function () {
                    return DoctorAccident::factory()->create();
                },
                'form_report_id' => function () {
                    return FormReport::factory()->create([
                        'form_id' => Form::factory()->create()
                    ]);
                },
                'city_id' => City::factory()->create(),
                /*'assistant_invoice_id' => function () {
                    return factory(\App\Invoice::class)->create();
                },
                // link to the form
                'assistant_guarantee_id' => function () {
                    return factory(\App\Invoice::class)->create();
                }*/
            ])->each(function (Accident $accident) {
                $accident->checkpoints()->save(AccidentCheckpoint::factory()->create());

                $accident->caseable->services()->attach(Service::factory()->create());

                $accident->caseable->diagnostics()->attach(Diagnostic::factory()->create());

                $accident->caseable->surveys()->attach(Survey::factory()->create());
                $accident->caseable->surveys()->attach(Survey::factory()->create());
            });
        }
    }
}
