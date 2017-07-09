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
    // FMI I can return needed when it will be required
    'allowedOrigins' => [env('CORS_ALLOW_ORIGIN_DIRECTOR'), env('CORS_ALLOW_ORIGIN_DOCTOR')],
    'allowedHeaders' => ['*'],
    'allowedMethods' => ['*'],
    'exposedHeaders' => [],
    'maxAge' => 0,
];

