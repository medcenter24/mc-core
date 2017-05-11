<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */


namespace App\Services\Import;


use App\Support\Core\Configurable;
use Illuminate\Support\Facades\Log;

class Importer extends Configurable
{

    public function import($path = '')
    {
        foreach ($this->getOption('registeredDataProviders') as $registeredProvider) {

            Log::debug('Checking that provider match to path', [
                'provider' => $registeredProvider,
                'path' => $path
            ]);

            if ($provider = $registeredProvider->load($path)->check()) {

                Log::debug('Provider has matched path', [
                    'provider' => $registeredProvider,
                    'path' => $path
                ]);

                $provider->import();
            }
        }
    }
}
