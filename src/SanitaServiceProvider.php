<?php

namespace Yamil\Sanita;

use Illuminate\Support\ServiceProvider;
use Yamil\Sanita\Console\CheckHorizonStatusCommand;

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
        /* if (!class_exists(\Laravel\Horizon\Horizon::class)) {
            throw new \RuntimeException('Laravel Horizon is required to use Sanita.');
        } */
        $configPath = __DIR__ . '/../config/sanita-config.php';
        $this->publishes([$configPath => config_path('sanita.php')], 'config');
    }
}
