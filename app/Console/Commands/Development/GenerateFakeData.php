<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2020 (original work) MedCenter24.com;
 */

declare(strict_types=1);

namespace medcenter24\mcCore\App\Console\Commands\Development;

use Illuminate\Console\Command;
use medcenter24\mcCore\App\Entity\Accident;
use medcenter24\mcCore\App\Entity\Assistant;
use medcenter24\mcCore\App\Entity\City;
use medcenter24\mcCore\App\Entity\Diagnostic;
use medcenter24\mcCore\App\Entity\Doctor;
use medcenter24\mcCore\App\Entity\Form;
use medcenter24\mcCore\App\Entity\Invoice;
use medcenter24\mcCore\App\Entity\Patient;
use medcenter24\mcCore\App\Entity\Service;
use medcenter24\mcCore\App\Entity\Survey;

class GenerateFakeData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:fake';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate development data';

    public function handle()
    {
        $amount = 1000;
        if (!app()->environment('production')) {
            factory(Assistant::class, $amount)->create();
            factory(Doctor::class, $amount)->create();
            factory(City::class, $amount)->create();
            factory(Accident::class, $amount)->create();
            factory(Patient::class, $amount)->create();
            factory(Service::class, $amount)->create();
            factory(Survey::class, $amount)->create();
            factory(Diagnostic::class, $amount)->create();
            factory(Form::class, $amount)->create();
            factory(Invoice::class, $amount)->create();
        }
    }
}
