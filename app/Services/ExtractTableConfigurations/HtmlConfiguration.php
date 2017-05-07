<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Services\ExtractTableConfigurations;


use App\Services\ExtractTableFromArrayService;

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
