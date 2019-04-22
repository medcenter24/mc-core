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

namespace medcenter24\mcCore\App\Services\ExtractTableConfigurations;


use medcenter24\mcCore\App\Services\ExtractTableFromArrayService;

class HtmlConfiguration
{
    public static function getConfig()
    {
        return [
            ExtractTableFromArrayService::CONFIG_FIRST_INDEX => true,
            ExtractTableFromArrayService::CONFIG_TABLE => ['table'],
            ExtractTableFromArrayService::CONFIG_ROW => ['tr'],
            ExtractTableFromArrayService::CONFIG_CEIL => ['td', 'th'],
        ];
    }
}
