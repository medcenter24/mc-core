<?php
/**
 * Copyright (c) 2018.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Providers;


use App\AccidentStatus;
use App\HospitalAccident;
use App\Services\AccidentStatusesService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        HospitalAccident::saved(function ($hospitalAccident) {
            $statusesService = new AccidentStatusesService();

            $events = [
                'hospital_guarantee_id' => [
                    'status' => AccidentStatusesService::STATUS_HOSPITAL_GUARANTEE,
                    'type' => AccidentStatusesService::TYPE_HOSPITAL,
                ],
                'hospital_invoice_id' => [
                    'status' => AccidentStatusesService::STATUS_HOSPITAL_INVOICE,
                    'type' => AccidentStatusesService::TYPE_HOSPITAL,
                ],
                'assistant_invoice_id' => [
                    'status' => AccidentStatusesService::STATUS_ASSISTANT_INVOICE,
                    'type' => AccidentStatusesService::TYPE_ASSISTANT,
                ],
                'assistant_guarantee_id' => [
                    'status' => AccidentStatusesService::STATUS_ASSISTANT_GUARANTEE,
                    'type' => AccidentStatusesService::TYPE_ASSISTANT,
                ],
                'assistant_paid' => [
                    'status' => AccidentStatusesService::STATUS_PAID,
                    'type' => AccidentStatusesService::TYPE_ASSISTANT,
                ],
            ];

            foreach ($events as $key=>$event) {
                if ($hospitalAccident->$key) {
                    $statusesService->set($hospitalAccident->accident, AccidentStatus::firstOrCreate([
                        'title' => $event['status'],
                        'type' => $event['type'],
                    ]), 'Assigned on change');
                }
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            // fyi needs full path, because I don't have this lib on the prod
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }
}
