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

use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\AccidentStatus;
use medcenter24\mcCore\App\Entity\AccidentStatusHistory;
use medcenter24\mcCore\App\Entity\AccidentType;
use medcenter24\mcCore\App\Entity\Assistant;
use medcenter24\mcCore\App\Entity\DoctorAccident;
use medcenter24\mcCore\App\Entity\Form;
use medcenter24\mcCore\App\Entity\FormReport;
use medcenter24\mcCore\App\Entity\Patient;
use medcenter24\mcCore\App\Services\Entity\AccidentTypeService;
use medcenter24\mcCore\App\Entity\User;
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
