<?php

namespace App\Http\Middleware;

use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [];

    public function __construct(Application $app, Encrypter $encrypter)
    {
        $this->except = array_merge($this->except, [
            '/telegram/'.env('TELEGRAM_WEBHOOK_PREFIX')
        ]);
        parent::__construct($app, $encrypter);
    }
}
