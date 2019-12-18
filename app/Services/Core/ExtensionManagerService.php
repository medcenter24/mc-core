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
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019  (original work) Open Assessment Technologies SA;
 *
 * @author Oleksandr Zagovorychev <zagovorichev@gmail.com>
 */

namespace medcenter24\mcCore\App\Services\Core;


use medcenter24\mcCore\App\Exceptions\InconsistentDataException;
use medcenter24\mcCore\App\Helpers\FileHelper;

class ExtensionManagerService
{
    /**
     * Checks that extension installed
     * @param string $extName
     * @return bool
     */
    public function has(string $extName = ''): bool
    {
        $status = false;
        $modulesFilePath = app_path() . '/../modules_statuses.json';
        try {
            $modules = json_decode(FileHelper::getContent($modulesFilePath), 1);
            $status = array_key_exists($extName, $modules) && $modules[$extName];
        } catch (InconsistentDataException $e) {}
        return $status;
    }
}
