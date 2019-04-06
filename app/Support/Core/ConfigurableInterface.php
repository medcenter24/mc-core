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

namespace App\Support\Core;


/**
 * Interface for configurable classes
 *
 * All classes implementing this interface are  configurable using the constructor or
 * setOption calls. This is the base for many Solarium classes, providing a
 * uniform interface for various models.
 */
interface ConfigurableInterface
{

    /**
     * Set options
     *
     * If $options is an object it will be converted into an array by called
     * it's toArray method. This is compatible with the Zend_Config classes in
     * Zend Framework, but can also easily be implemented in any other object.
     *
     * @throws InvalidArgumentException
     * @param  array|\Zend_Config       $options
     * @param  boolean                  $overwrite True for overwriting existing options, false
     *                                             for merging (new values overwrite old ones if needed)
     *
     * @return void
     */
    public function setOptions($options, $overwrite = false);

    /**
     * Get an option value by name
     *
     * If the option is empty or not set a NULL value will be returned.
     *
     * @param  string $name
     * @return mixed
     */
    public function getOption($name);

    /**
     * Get all options
     *
     * @return array
     */
    public function getOptions();
}
