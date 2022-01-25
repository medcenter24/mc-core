<?php
declare(strict_types=1);

return [
    'webhook' => env('LOG_SLACK_WEBHOOK_URL', ''),
    'logLevel' => env('LOG_SLACK_LEVEL', 'critical'),
];
