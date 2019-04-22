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

use medcenter24\mcCore\App\Form;
use medcenter24\mcCore\App\FormReport;
use Illuminate\Database\Seeder;

class FormReportsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment('production') && FormReport::all()->count()) {
            return;
        } elseif (!App::environment('production')) {
            FormReport::truncate();
            factory(FormReport::class, 10)->create([
                'form_id' => function () {
                    return factory(Form::class)->create([
                        'title' => 'Accident Report Form',
                        'description' => 'Form has been generated by ModelFactory for FormReport',
                        'template' => '<p>Form for :className</p><p>Generated by <b>:generatorName</b></p>',
                        'variables' => 'className,generatorName',
                    ])->id;
                },
            ]);
        }
    }
}
