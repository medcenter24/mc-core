<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

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
        $this->call(UsersTableSeeder::class);
        $this->call(AccidentTypesTableSeeder::class);

        if (env('APP_ENV') != 'production') {
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
            $this->call(DatePeriodSeeder::class);
        }
    }
}
