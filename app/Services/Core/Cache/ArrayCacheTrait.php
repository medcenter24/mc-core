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
 * Copyright (c) 2019 (original work) MedCenter24.com;
 */

namespace medcenter24\mcCore\App\Services\Core\Cache;


trait ArrayCacheTrait
{
    private $cache = [];

    /**
     * @param $key
     * @return mixed
     */
    public function getCache(string $key)
    {
        return $this->hasCache($key) ? $this->cache[$key] : null;
    }

    /**
     * @param $key
     * @param $value
     */
    public function setCache(string $key, $value): void
    {
        $this->cache[$key] = $value;
    }

    /**
     * Drop cached data
     */
    public function dropCache(): void
    {
        $this->cache = [];
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasCache(string $key): bool
    {
        return array_key_exists($key, $this->cache);
    }
}
