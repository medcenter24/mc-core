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

use medcenter24\mcCore\App\Accident;
use medcenter24\mcCore\App\AccidentCheckpoint;
use medcenter24\mcCore\App\AccidentStatus;
use medcenter24\mcCore\App\AccidentType;
use medcenter24\mcCore\App\Assistant;
use medcenter24\mcCore\App\Diagnostic;
use medcenter24\mcCore\App\DoctorAccident;
use medcenter24\mcCore\App\DoctorService;
use medcenter24\mcCore\App\DoctorSurvey;
use medcenter24\mcCore\App\Form;
use medcenter24\mcCore\App\FormReport;
use medcenter24\mcCore\App\Patient;
use medcenter24\mcCore\App\User;
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
            factory(Accident::class, 5)->create([
                'created_by' => factory(User::class)->create(),
                'patient_id' => factory(Patient::class)->create(),
                'accident_type_id' => function () {
                    return !AccidentType::count()
                        ? factory(AccidentType::class)->create()
                        : AccidentType::first();
                },
                'accident_status_id' => \App\AccidentStatus::firstOrCreate(AccidentStatusesTableSeeder::ACCIDENT_STATUSES[0]), // new
                'assistant_id' => function () {
                    return factory(Assistant::class)->create();
                },
                'caseable_id' => function () {
                    return factory(DoctorAccident::class)->create();
                },
                'form_report_id' => function () {
                    return factory(FormReport::class)->create([
                        'form_id' => function () {
                            return factory(Form::class)->create();
                        }
                    ]);
                },
                'city_id' => function () {
                    return factory(\App\City::class)->create();
                },
                /*'assistant_invoice_id' => function () {
                    return factory(\App\Invoice::class)->create();
                },
                // link to the form
                'assistant_guarantee_id' => function () {
                    return factory(\App\Invoice::class)->create();
                }*/
            ])->each(function (Accident $accident) {
                $accident->checkpoints()->save(factory(AccidentCheckpoint::class)->create());

                $accident->services()->attach(factory(DoctorService::class)->create());
                $accident->services()->attach(factory(DoctorService::class)->create());
                $accident->caseable->services()->attach(factory(DoctorService::class)->create());

                $accident->diagnostics()->attach(factory(Diagnostic::class)->create());
                $accident->diagnostics()->attach(factory(Diagnostic::class)->create());
                $accident->caseable->diagnostics()->attach(factory(Diagnostic::class)->create());

                $accident->surveys()->attach(factory(DoctorSurvey::class)->create());
                $accident->surveys()->attach(factory(DoctorSurvey::class)->create());
                $accident->caseable->surveys()->attach(factory(DoctorSurvey::class)->create());
                $accident->caseable->surveys()->attach(factory(DoctorSurvey::class)->create());
            });
        }
    }
}
