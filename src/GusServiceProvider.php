<?php

namespace Ljozwiak\Gus;

use Illuminate\Support\ServiceProvider;
use Ljozwiak\Gus\Commands\GusCommand;

class GusServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Ljozwiak\Gus\Http\Controllers\GusController');
        $this->mergeConfigFrom(__DIR__.'/config/gus.php', 'gus');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GusCommand::class,
            ]);
        }

        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'gus');
        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/gus'),
        ],'views');
        $this->publishes([
            __DIR__.'/config/gus.php' =>   config_path('gus.php'),
        ], 'config');
    }
}
