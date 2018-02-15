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
     * @param string $token
     * @return bool
     */
    public function isValidInvite($token = '')
    {
        $invite = $this->getInviteByToken($token);
        return $invite && $invite->id ? true : false;
    }

    /**
     * Load invite by the token
     * @param $token
     * @return Invite
     */
    public function getInviteByToken($token)
    {
        $now = Carbon::now();
        \Log::info('Token is not valid', [
            'token' => $token,
            'now' => $now->format(config('date.systemFormat')),
        ]);
        return Invite::where('token', $token)
            ->where('valid_from', '<=', $now->format(config('date.systemFormat')))
            ->where('valid_to', '>=', $now->format(config('date.systemFormat')))
            ->first();
    }

    /**
     * Clean all outdated tokens
     */
    public function clean()
    {
        Invite::where('valid_to', '<=', Carbon::now())->delete();
    }
}
