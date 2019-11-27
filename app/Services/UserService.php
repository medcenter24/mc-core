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

namespace medcenter24\mcCore\App\Services;


use medcenter24\mcCore\App\Support\Core\Configurable;
use medcenter24\mcCore\App\User;

class UserService extends AbstractModelService
    // extends Configurable don't know why it is here, but if I need it Have to create new Configurable service instead
{
    // We can define rule in the configuration maybe
    /**
     * @param string $email
     * @return bool
     */
    public function isValidEmail(string $email): bool
    {
        return !empty($email);
    }

    /**
     * @param string $password
     * @return bool
     */
    public function isValidPassword(string $password): bool
    {
        return !empty($password);
    }

    /**
     * Active user timezone or default timezone
     * @return string
     */
    public function getTimezone(): string
    {
        $timezone = 'UTC';
        if (auth()->check() && auth()->user()->timezone) {
            $timezone = auth()->user()->timezone;
        }
        return $timezone;
    }

    /**
     * Name of the Model (ex: City::class)
     * @return string
     */
    protected function getClassName(): string
    {
        return User::class;
    }

    /**
     * Initialize defaults to avoid database exceptions
     * (different storage have different rules, so it is correct to set defaults instead of nothing)
     * @return array
     */
    protected function getRequiredFields(): array
    {
        return [
            'name' => '',
            'email' => '',
            'phone' => '',
            'lang' => 'en',
            'timezone' => 'UTC'
        ];
    }
}
