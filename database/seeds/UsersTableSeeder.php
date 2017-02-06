<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();

        factory(User::class)->make(['email' => 'admin@mail.com', 'name' => 'Abigail'])->save();
        factory(User::class)->make(['email' => 'doctor@mail.com', 'name' => 'Peter'])->save();
        factory(User::class)->make(['email' => 'director@mail.com', 'name' => 'Samantha'])->save();
        $users = factory(User::class, 10)->make();
        foreach($users as $user) {
            $user->save();
        }
    }
}
