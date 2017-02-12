<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Unit;


use App\Role;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserRoleTest extends TestCase
{

    use DatabaseMigrations;

    public function testCreateUser()
    {
        $user = $this->createUser();

        self::assertEquals('test', $user->name);
        self::assertGreaterThan(0, $user->id);

        $t = User::findOrFail($user->id);
        self::assertEquals($user->name, $t->name);

        return $user;
    }

    public function testUserEdit()
    {
        $user = $this->createUser();

        self::assertEquals('test', $user->name);
        $user->name = 'Lui';
        $user->save();
        $t = User::findOrFail($user->id);
        self::assertEquals($user->name, $t->name);
    }

    public function testSoftUserDelete()
    {
        $user = $this->createUser();
        $id = $user->id;
        $user->delete();
        $_user = User::find($id);
        self::assertNull($_user);
        $_user = User::withTrashed()->find($id);
        self::assertEquals($id, $_user->id);
    }

    public function testForceDelete()
    {
        $user = $this->createUser();
        $id = $user->id;
        $user->forceDelete();
        $_user = User::find($id);
        self::assertNull($_user);
        $_user = User::withTrashed()->find($id);
        self::assertNull($_user);
    }

    /**
     * @return User
     */
    private function createUser(): User
    {
        $user = new User();
        $user->name = 'test';
        $user->email = 'fake@user.unit';
        $user->password = '';
        $user->save();
        return $user;
    }

    private function createRole()
    {
        $role = new Role();
        $role->title = 'hola';
        $role->save();
        return $role;
    }

    public function testCreateRole()
    {
        $role = $this->createRole();
        $_role = Role::find($role->id);
        self::assertEquals($role->id, $_role->id);
    }

    public function testUserRole()
    {
        $user = $this->createUser();
        $role = $this->createRole();
        self::assertEquals(0, $user->roles()->count());
        $user->roles()->attach($role);
        self::assertEquals(1, $user->roles()->count());
        $user->roles()->detach($role);
        self::assertEquals(0, $user->roles()->count());
    }
}
