<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services;


use App\User;

/**
 * Permissions and role interface
 *
 * Class RoleService
 * @package App\Services
 */
class RoleService
{
    const LOGIN_ROLE = 'login';
    const DIRECTOR_ROLE = 'director';
    const DOCTOR_ROLE = 'doctor';
    const ADMIN_ROLE = 'admin';

    /**
     * Check that user has role permissions
     *
     * @param User $user
     * @param string $role
     * @return mixed
     */
    public function hasRole(User $user = null, string $role)
    {
        return $user ? $user->roles()->where('title', $role)->count() : false;
    }
}
