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

namespace medcenter24\mcCore\App\Console\Commands;

use medcenter24\mcCore\App\Role;
use medcenter24\mcCore\App\Services\RoleService;
use medcenter24\mcCore\App\Services\UserService;
use medcenter24\mcCore\App\User;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;

/**
 * sudo -u www-data /usr/bin/php7.2 artisan user:add mail@mail.com login,admin passWord
 *
 * Class CreateUserCommand
 * @package medcenter24\mcCore\App\Console\Commands
 */
class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:add
        {email? : Email}
        {roles? : Role [admin, login, doctor, director]}
        {password? : Secrete phrase}
        {name? : Users name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new user';

    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $username;
    /**
     * @var array
     */
    private $roles;
    /**
     * @var string
     */
    private $password;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var RoleService
     */
    private $rolesService;

    /**
     * Execute the console command.
     *
     * @param UserService $userService
     * @param RoleService $roleService
     */
    public function handle(UserService $userService, RoleService $roleService): void
    {
        $this->userService = $userService;
        $this->rolesService = $roleService;

        $this->setEmail();
        $this->setRoles();
        $this->setPassword();
        $this->setUserName();

        $this->showUser();

        if ($this->confirm('Do you wish to create this user?')) {
            $this->createUser();
        }
    }

    private function showUser(): void
    {
        $headers = ['E-Mail', 'Name', 'Roles'];
        $rows = [$this->email, $this->username, implode($this->roles, ',')];
        $this->table($headers, [$rows]);
    }

    private function setEmail(): void
    {
        $email = (string)$this->argument('email');
        if (!$this->userService->isValidEmail($email)) {
            do {
                $email = (string)$this->ask('`' . $email . '` is not correct E - Mail, fix it please');
            } while(!$this->userService->isValidEmail($email));
        }

        $this->email = $email;
    }

    private function setRoles(): void
    {
        $roles = $this->argument('roles');
        if (!$roles) {
            $roles = (string)$this->ask('Enter users roles [' . implode(',', RoleService::ROLES) . ']:', 'login');
        }

        $roles = explode(',', $roles);
        if (!$this->rolesService->isValidRoles($roles)) {
            do {
                $roles = $this->ask('Provided role(s) is/are not correct, fix them:',
                    implode(',', array_intersect($roles, RoleService::ROLES)));
                $roles = explode(',', $roles);
            } while(!$this->rolesService->isValidRoles($roles));
        }

        $this->roles = $roles;
    }

    private function setUserName(): void
    {
        $this->username = (string)$this->argument('name');
        if (!$this->username) {
            $this->username = (string)$this->ask('Enter user name');
        }
    }

    private function setPassword(): void
    {
        $password = (string)$this->argument('password');

        if (!$this->userService->isValidPassword($password)) {
            do {
                $password = (string)$this->secret('Enter new password');
            } while(!$this->userService->isValidPassword($password));
        }
        $this->password = $password;
    }
    
    private function createUser(): void
    {
        $this->info('Start creating...');
        try {
            /** @var User $user */
            $user = User::firstOrCreate([
                'email' => $this->email,
                'password' => bcrypt($this->password),
                'name' => $this->username,
            ]);
        } catch (QueryException $e) {
            $this->error('Can not create a user (duplicate?)');
            return;
        }

        $_roles = [];
        foreach ($this->roles as $role) {
            $_role = Role::where('title', $role)->first();
            if ($_role) {
                $_roles[] = $_role->id;
            }
        }
        if (!count($_roles)) {
            $this->warn('Roles was not assigned [not found]');
        }
        $user->roles()->attach($_roles);

        $this->info('User [' . $user->email . '] created');
    }
}
