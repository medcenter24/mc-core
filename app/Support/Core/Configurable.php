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
declare(strict_types=1);

namespace medcenter24\mcCore\App\Support\Core;

use medcenter24\mcCore\App\Exceptions\CommonException;

/**
 * Make things more configurable
 *
 * Class Configurable
 * @package medcenter24\mcCore\App\Services
 */
abstract class Configurable implements ConfigurableInterface
{
    private array $options = [];

    /**
     * public constructor to allow the object to be recreated from php code
     *
     * @param mixed $options
     * @throws CommonException
     */
    public function __construct(mixed $options = []) {
        $this->setOptions($options);
    }

    /**
     * Set option
     *
     * @param $name
     * @param $value
     */
    public function setOption($name, $value): void
    {
        $this->options[$name] = $value;
    }

    /**
     * Set options
     *
     * @param mixed $options
     * @param bool $overwrite
     * @return void
     * @throws CommonException
     */
    public function setOptions($options, $overwrite = false): void
    {
        if (!is_array($options)) {
            if (is_object($options) && method_exists($options, 'toArray')) {
                $options = $options->toArray();
            } else {
                throw new CommonException('Options submitted to '.static::class.' must be an array or implement toArray');
            }
        }

        if (!$overwrite) {
            $this->options = array_merge($this->getOptions(), $options);
        } else {
             $this->options = $options;
        }
    }

    /**
     * Returns whenever or not the option is defined
     *
     * @param  string $name
     * @return bool
     */
    public function hasOption($name): bool
    {
        return isset($this->options[$name]);
    }

    /**
     * Get an option value by name
     *
     * If the option is empty or not set a NULL value will be returned.
     *
     * @param  string $name
     * @return mixed
     */
    public function getOption($name) {
        return $this->options[$name] ?? null;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
