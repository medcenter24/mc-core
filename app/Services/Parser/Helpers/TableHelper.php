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

/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 06.05.2017
 * Time: 20:25
 */

namespace App\Services\Parser\Helpers;


class TableHelper
{
    /**
     * Tag which determined table
     */
    const TABLE_TAG = 'table_tag';
    /**
     * Tag which determined row of the table
     */
    const TABLE_ROW = 'table_row';
    /**
     * Tag which determined column of the table
     */
    const TABLE_CEIL = 'table_ceil';

    /**
     * Use first row of the table as index for data
     */
    const FIRST_INDEX_ROW = 'first_index_row';

    const FIELD_KEY = 'key';
    const FIELD_DATA = 'data';

    public static function getKeyValueFromArray(array $table)
    {

    }
}
