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

namespace medcenter24\mcCore\App\Models\Installer\Params\Env;


use Illuminate\Support\Str;
use medcenter24\mcCore\App\Contract\Installer\EnvParam;
use medcenter24\mcCore\App\Models\Installer\Params\StringParam;
use medcenter24\mcCore\App\Services\Core\EnvironmentService;

class EnvAppKeyParam extends StringParam implements EnvParam
{
    /**
     * @var string
     */
    private $value;

    public function getParamName(): string
    {
        return EnvironmentService::PROP_APP_KEY;
    }

    public function defaultValue(): string
    {
        return Str::random(32);
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        if ($this->value === null || empty($this->value)) {
            $this->value = $this->defaultValue();
        }
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return parent::isValid() && !empty($this->getValue()) && mb_strlen($this->getValue()) >=3;
    }

    public function question(): string
    {
        return 'At least 3 symbols, or use default';
    }
}
