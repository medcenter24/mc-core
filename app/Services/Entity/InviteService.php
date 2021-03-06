<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2020 (original work) MedCenter24.com;
 */

declare(strict_types = 1);

namespace medcenter24\mcCore\App\Services\Entity;

use ErrorException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use medcenter24\mcCore\App\Entity\Invite;
use medcenter24\mcCore\App\Entity\User;
use Carbon\Carbon;

class InviteService extends AbstractModelService
{
    public const FIELD_USER_ID = 'user_id';
    public const FIELD_TOKEN = 'token';
    public const FIELD_VALID_FROM = 'valid_from';
    public const FIELD_VALID_TO = 'valid_to';

    public const FILLABLE = [
        self::FIELD_USER_ID,
        self::FIELD_TOKEN,
        self::FIELD_VALID_FROM,
        self::FIELD_VALID_TO,
    ];

    public const DATE_FIELDS = [
        self::FIELD_VALID_TO,
        self::FIELD_VALID_FROM,
        self::FIELD_CREATED_AT,
        self::FIELD_UPDATED_AT,
    ];

    public const VISIBLE = [
        self::FIELD_ID,
        self::FIELD_USER_ID,
        self::FIELD_TOKEN,
        self::FIELD_VALID_FROM,
        self::FIELD_VALID_TO,
    ];

    public const UPDATABLE = [
        self::FIELD_VALID_FROM,
        self::FIELD_VALID_TO,
    ];

    /**
     * Generate new invite for the user
     * @param User $user
     * @return mixed
     * @throws ErrorException
     */
    public function generate(User $user)
    {
        if (!$user->id) {
            throw new ErrorException('Token can not be generated without user');
        }

        $invite = Invite::create([
            'user_id' => $user->id,
            'token' => Str::random(21),
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
        Log::info('Token is not valid', [
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

    /**
     * @inheritDoc
     */
    protected function getClassName(): string
    {
        return Invite::class;
    }

    /**
     * @inheritDoc
     */
    protected function getFillableFieldDefaults(): array
    {
        return [
            self::FIELD_USER_ID => 0,
            self::FIELD_TOKEN => '',
            self::FIELD_VALID_FROM => '',
            self::FIELD_VALID_TO => '',
        ];
    }
}
