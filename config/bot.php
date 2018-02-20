<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

return [
    'connections' => [
        'default' => 'telegram',
        'telegram' => [
            'active' => true,
            'class' => \App\Services\Bot\Drivers\TelegramBot::class,
            'config' => config('telegram', []),
        ],
        'slack' => [
            'active' => false,
        ],
        'viber' => [
            'active' => false,
        ]
    ]
];
