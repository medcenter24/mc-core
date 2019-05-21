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

namespace medcenter24\mcCore\App\Models\Installer\Params;


use medcenter24\mcCore\App\Contract\Installer\InstallerParam;
use medcenter24\mcCore\App\Exceptions\InconsistentDataException;

/**
 * Class ConfigurableParam
 * @package medcenter24\mcCore\App\Services\Installer
 */
abstract class ConfigurableParam implements InstallerParam
{
    /**
     * @var string
     */
    private $value;

    /**
     * Set new parameter
     * @param string $value
     * @throws InconsistentDataException
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
        if (!$this->isValid()) {
            throw new InconsistentDataException($this->getErrorMessage());
        }
    }

    /**
     * @return string
     */
    protected function getErrorMessage(): string
    {
        return 'Incorrect parameters value '. get_class($this);
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        if ($this->value === null) {
            $this->value = $this->defaultValue();
        }
        return $this->value;
    }

    abstract public function getParamName(): string;

    /**
     * Check if parameter is correct
     * @return bool
     */
    abstract public function isValid(): bool;

    /**
     * @return string
     */
    abstract public function defaultValue(): string;

    public function question(): string
    {
        return 'Enter the value for the param `' . $this->getParamName() . '`';
    }

    public function isRequired(): bool
    {
        return false;
    }
}
