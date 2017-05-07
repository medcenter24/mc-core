<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
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
