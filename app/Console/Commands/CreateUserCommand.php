<?php

namespace App\Console\Commands;

use App\Role;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;

/**
 * command artisan make:user --role=login,admin --email=mail@mail.com --pass=word
 * // not implemented, but if needed...
 *
 * Class CreateUserCommand
 * @package App\Console\Commands
 */
class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:add {email} {role} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $user = User::firstOrCreate([
                'email' => $this->argument('email'),
                'password' => bcrypt($this->argument('password'))
            ]);
        } catch (QueryException $e) {
            $this->error('Can not create a user (duplicate?)');
            return;
        }

        $roles = explode(',', $this->argument('role'));
        $_roles = [];
        foreach ($roles as $role) {
            $_role = Role::where('title', $role)->first();
            if ($_role) {
                $_roles[] = $_role->id;
            }
        }
        if (!count($_roles)) {
            $this->warn('Roles was not assigned [not found]');
        }
        $user->roles()->attach($_roles);
        
        $this->info('User ' . $this->argument('email') . ' created');
    }
}
