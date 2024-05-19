<?php

namespace App\Providers;

use App\Interfaces\RosterBuilderInterface;
use App\Repositories\RosterBuilderRepository;
use App\Interfaces\RosterFormatterInterface;
use App\Repositories\RosterFormatterRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(RosterBuilderInterface::class, RosterBuilderRepository::class);
        $this->app->bind(RosterFormatterInterface::class, RosterFormatterRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
