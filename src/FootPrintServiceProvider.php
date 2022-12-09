<?php


namespace Brokecode\Footprint;

use Brokecode\Footprint\Models\Footprint;
use Illuminate\Support\ServiceProvider;

class FootPrintServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if (function_exists('config_path')) { // function not available and 'publish' not relevant in Lumen
            $this->publishes([
                __DIR__.'/../config/footprint.php' => config_path('footprint.php'),
            ], 'config');
        }

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/footprint.php',
            'footprint'
        );
    }

    public static function determineFootprintModel(): string
    {
        $footprintModel = config('footprint.model')['footprint'] ?? Footprint::class;

        if (! is_a($footprintModel, Footprint::class, true)
            || ! is_a($footprintModel, Model::class, true)) {
            throw InvalidConfiguration::modelIsNotValid($footprintModel);
        }

        return $footprintModel;
    }
}
