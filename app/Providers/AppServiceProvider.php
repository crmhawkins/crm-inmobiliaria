<?php

namespace App\Providers;

use App\Services\Idealista\IdealistaClient;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(IdealistaClient::class, function ($app) {
            return new IdealistaClient(
                $app->make(HttpFactory::class),
                $app->make(CacheRepository::class),
                config('services.idealista', [])
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFive();
    }
}
