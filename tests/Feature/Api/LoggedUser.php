<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace Tests\Feature\Api;


use App\User;

trait LoggedUser
{
    /**
     * @var User
     */
    private $user;

    public function getUser()
    {
        if (!$this->user) {
            // allow all roles for the test (maybe in the future I'll mock it for work with roles)
            // but it could be another unit test for testing only role access
            \Roles::shouldReceive('hasRole')
                ->andReturnUsing(function () {
                    return true;
                });
            /** @var User $user */
            $this->user = factory(User::class)->create(['password' => bcrypt('foo')]);
        }
        return $this->user;
    }
}
