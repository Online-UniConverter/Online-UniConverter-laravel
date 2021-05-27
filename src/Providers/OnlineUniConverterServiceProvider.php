<?php

namespace OnlineUniConverter\Laravel\Providers;

use OnlineUniConverter\OnlineUniConverter;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class OnlineUniConverterServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/onlineuniconverter.php' => config_path('onlineuniconverter.php'),
            ], 'config');
        }
        $this->mergeConfigFrom(__DIR__ . '/../config/onlineuniconverter.php', 'onlineuniconverter');
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton(OnlineUniConverter::class, function ($app) {
            return new OnlineUniConverter(
                Arr::only($app['config']['onlineuniconverter'],['apiKey'])
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [OnlineUniConverter::class];
    }
}
