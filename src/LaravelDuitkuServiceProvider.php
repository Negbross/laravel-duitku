<?php

namespace Negbross\LaravelDuitku;

use Negbross\LaravelDuitku\Console\LaravelDuitkuInstallCommand;
use Illuminate\Support\ServiceProvider;

class LaravelDuitkuServiceProvider extends ServiceProvider
{
    public const CONFIG_PATH = __DIR__ . '/../config/duitku.php';

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(self::CONFIG_PATH, 'duitku');

        $this->app->bind('laravel-duitku-api', function() {
            return new LaravelDuitkuAPI();
        });
        $this->app->bind('laravel-duitku-pop', function() {
            return new LaravelDuitkuPOP();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                self::CONFIG_PATH => config_path('duitku.php')
            ], 'config');

            $this->commands([LaravelDuitkuInstallCommand::class]);
        }
    }
}
