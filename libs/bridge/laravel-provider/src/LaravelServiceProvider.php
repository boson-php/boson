<?php

declare(strict_types=1);

namespace Boson\Bridge\Laravel\Provider;


use Illuminate\Support\ServiceProvider;

final class LaravelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/octane.php', 'octane'
        );
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/octane.php' => config_path('octane.php'),
            __DIR__.'/../stubs/boson' => base_path('boson'),
        ]);
    }
}
