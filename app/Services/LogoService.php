<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services;


use App\User;

class LogoService
{
    const DISC = 'logo';
    const FOLDER = 'media';

    public function setLogo(User $user, $file)
    {
        $user->addMedia($file)
            ->toMediaCollection(self::FOLDER, self::DISC);
        return true;
    }

    /**
     * @param User $user
     * @param User $changeableUser
     * @param RoleService $roleService
     * @return bool
     */
    public function checkAccess(User $user, User $changeableUser, RoleService $roleService)
    {
        return $roleService->hasRole($user, 'director')
            || ( $roleService->hasRole($user, 'doctor')
                && $changeableUser->id == $user->id );
    }
}
