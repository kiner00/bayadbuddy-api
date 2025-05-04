<?php

namespace App\Providers;

use App\Contracts\SmsSenderInterface;
use App\Services\SmsProviders\PhilSmsService;
use App\Services\SmsProviders\SemaphoreSmsService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SmsSenderInterface::class, PhilSmsService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
