<?php

namespace App\Providers;

use App\Contracts\PetstoreServiceInterface;
use App\Services\PetstoreService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services - DI.
     */
    public function register(): void
    {
        $this->app->bind(PetstoreServiceInterface::class, PetstoreService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
