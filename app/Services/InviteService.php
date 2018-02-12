<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services;


use App\Invite;
use App\User;
use Carbon\Carbon;

class InviteService
{
    /**
     * Generate new invite for the user
     * @param User $user
     * @return mixed
     * @throws \ErrorException
     */
    public function generate(User $user)
    {
        if (!$user->id) {
            throw new \ErrorException('Token can not be generated without user');
        }

        $invite = Invite::create([
            'user_id' => $user->id,
            'token' => str_random(21),
            'valid_from' => Carbon::now(),
            'valid_to' => Carbon::now()->addWeek(2),
        ]);

        return $invite;
    }

    /**
     * Check if invite is match to user, exists, and active
     * @param User $user
     * @param string $token
     * @return bool
     */
    public function isValidInvite(User $user, $token = '')
    {
        $now = Carbon::now();
        $invite = Invite::were('user_id', $user->id)
            ->where('token', $token)
            ->where('valid_from', '>=', $now)
            ->where('valid_to', '<=', $now)
            ->first();

        return $invite->id ? true : false;
    }

    public function clean()
    {
        Invite::where('valid_to', '<=', Carbon::now())->delete();
    }
}
