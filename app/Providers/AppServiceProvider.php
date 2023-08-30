<?php

namespace App\Providers;

use App\Contracts\CrawlerFactory;
use App\Services\DomCrawlerFactory;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CrawlerFactory::class, DomCrawlerFactory::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
