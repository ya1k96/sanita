<?php

namespace Sanita;

use Illuminate\Support\ServiceProvider;
use MyVendor\HorizonNotifier\Console\CheckHorizonStatusCommand;

class SanitaServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            CheckHorizonStatusCommand::class,
        ]);
    }

    public function boot()
    {
        if (!class_exists(\Laravel\Horizon\Horizon::class)) {
            throw new \RuntimeException('Laravel Horizon is required to use Sanita.');
        }

        $this->publishes([
            __DIR__.'/Config/sanita-config.php' => config_path('sanita.php'),
        ], 'config');

        $this->loadRoutesFrom(__DIR__.'/routes.php');
    }
}
