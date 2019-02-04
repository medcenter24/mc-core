<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\Accident;
use App\AccidentCheckpoint;
use App\AccidentStatus;
use App\AccidentType;
use App\Assistant;
use App\Diagnostic;
use App\DoctorAccident;
use App\DoctorService;
use App\DoctorSurvey;
use App\Form;
use App\FormReport;
use App\Patient;
use App\User;
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
