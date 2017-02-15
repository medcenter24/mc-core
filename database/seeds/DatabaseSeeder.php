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
        $this->call(DocumentsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(DoctorsTableSeeder::class);
        $this->call(AccidentDoctorTableSeeder::class);
        $this->call(AssistantTableSeeder::class);
        $this->call(AccidentCheckpointTableSeeder::class);
        $this->call(FormsTableSeeder::class);
        $this->call(FormReportsTableSeeder::class);
    }
}
