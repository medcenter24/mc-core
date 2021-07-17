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

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AccidentStatusesTableSeeder::class);
        $this->call(ScenariosTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        // use command artisan add:user --role=login,admin --email=mail@mail.com --password=word
        // $this->call(UsersTableSeeder::class);
        $this->call(AccidentTypesTableSeeder::class);
        $this->call(FinanceCurrenciesTableSeeder::class);
        /*if (App::environment('production')) {
            $this->call(UsersTableSeeder::class);
            $this->call(CitiesTableSeeder::class);
            $this->call(DiagnosticsTableSeeder::class);
            $this->call(DocumentsTableSeeder::class);
            $this->call(DoctorsTableSeeder::class);
            $this->call(DoctorAccidentsTableSeeder::class);
            $this->call(AssistantTableSeeder::class);
            $this->call(AccidentCheckpointsTableSeeder::class);
            $this->call(FormsTableSeeder::class);
            $this->call(FormReportsTableSeeder::class);
            $this->call(PatientsTableSeeder::class);
            $this->call(DoctorServicesTableSeeder::class);
            $this->call(AccidentStatusHistoriesTableSeeder::class);
            $this->call(AccidentsTableSeeder::class);
            $this->call(HospitalsTableSeeder::class);
            $this->call(InvoicesTableSeeder::class);
            $this->call(DatePeriodTableSeeder::class);
            $this->call(FinanceConditionTableSeeder::class);
            $this->call(FinanceStorageTableSeeder::class);
        }*/
    }
}
