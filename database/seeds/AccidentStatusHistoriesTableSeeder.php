<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\Accident;
use App\AccidentStatus;
use App\AccidentStatusHistory;
use App\AccidentType;
use App\Assistant;
use App\DoctorAccident;
use App\Form;
use App\FormReport;
use App\Patient;
use App\Services\AccidentTypeService;
use App\User;
use Illuminate\Database\Seeder;

class AccidentStatusHistoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment('production') && AccidentStatusHistory::all()->count()) {
            return;
        } elseif (!App::environment('production')) {
            AccidentStatusHistory::truncate();
            factory(AccidentStatusHistory::class, 10)->create([
                'historyable_id' => function () {
                    // could be each of accident Doctor_Accident Accident Hospital_Accident ...
                    return factory(Accident::class)->create([
                        'created_by' => factory(User::class)->create(),
                        'patient_id' => factory(Patient::class)->create(),
                        'accident_type_id' => function () {
                            return !AccidentType::count()
                                ? factory(AccidentType::class)->create()
                                : AccidentType::first();
                        },
                        'accident_status_id' => function () {
                            return AccidentStatus::count()
                                ? AccidentStatus::first()
                                : factory(AccidentStatus::class)->create();
                        },
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
                    ]);
                },
            ]);
        }
    }
}
