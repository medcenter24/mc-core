<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services;


use App\User;

class RoleService
{
    public function hasRole(User $user, string $role)
    {
        return $user->roles()->where('title', $role)->count();
    }
}
