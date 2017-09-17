<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Unit\Services;

use App\Role;
use App\Services\RoleService;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RoleServiceTest extends TestCase
{
    /**
     * @var RoleService
     */
    private $service;

    private $user;

    protected function setUp()
    {
        parent::setUp();

        $this->service = new RoleService();

        $role = $this->prophesize(Role::class)->reveal();
        $role->title = 'roleName';

        $storage = new class {
            private $sameRole = false;
            public function where($title, $role)
            {
                $this->sameRole = $role == 'roleName';
                return $this;
            }
            public function count() {
                return $this->sameRole;
            }
        };

        $user = $this->prophesize(User::class);
        $user->roles()->willReturn($storage);

        $this->user = $user->reveal();
    }

    public function testHasRole()
    {
        self::assertTrue($this->service->hasRole($this->user, 'roleName'));
        self::assertFalse($this->service->hasRole($this->user, 'incorrectRoleName'));
    }
}
