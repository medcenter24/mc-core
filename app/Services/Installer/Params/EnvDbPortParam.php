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

namespace App\Services\Installer\Params;


use App\Services\Installer\ConfigurableParam;
use App\Services\EnvironmentService;

/**
 * @package App\Services\Installer\Params
 */
class EnvDbPortParam extends ConfigurableParam implements EnvParam
{
    public function getParamName(): string
    {
        return EnvironmentService::PROP_DB_PORT;
    }

    public function defaultValue(): string
    {
        return '3306';
    }

    public function isValid(): bool
    {
        return !empty($this->getValue());
    }

    public function question(string $value = ''): string
    {
        return 'Database port';
    }
}