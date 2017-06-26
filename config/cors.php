<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

return [
    /*
     |--------------------------------------------------------------------------
     | Laravel CORS
     |--------------------------------------------------------------------------
     |
     | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
     | to accept any value.
     |
     */
    'supportsCredentials' => true,
    // 'allowedOrigins' => ['*'], // importer won't work with *
    'allowedOrigins' => [env('APP_ENV') == 'production'
        ? '' // todo paths to the doctor and director sites
        : 'http://localhost:4200'], // only development environment on the localhost
    'allowedHeaders' => ['*'],
    'allowedMethods' => ['*'],
    'exposedHeaders' => [],
    'maxAge' => 0,
];

