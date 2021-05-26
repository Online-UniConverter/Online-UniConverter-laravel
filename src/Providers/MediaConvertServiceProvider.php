<?php

namespace OnlineUniConvert\Laravel\Providers;

use OnlineUniConvert\OnlineUniConvert;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class OnlineUniConvertServiceProvider extends ServiceProvider implements DeferrableProvider
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
                __DIR__ . '/../config/OnlineUniConvert.php' => config_path('OnlineUniConvert.php'),
            ], 'config');
        }
        $this->mergeConfigFrom(__DIR__ . '/../config/OnlineUniConvert.php', 'OnlineUniConvert');

    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton(OnlineUniConvert::class, function ($app) {
            return new OnlineUniConvert(
                Arr::only($app['config']['OnlineUniConvert'],
                    ['apiKey']
                )
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
        return [OnlineUniConvert::class];
    }
}
