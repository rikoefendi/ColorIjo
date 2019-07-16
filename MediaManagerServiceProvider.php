<?php

/**
 *
 */
namespace ColorIjo\MediaManager;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use ColorIjo\MediaManager\MedmaFacade;
use ColorIjo\MediaManager\MediaManager;

class MediaManagerServiceProvider extends ServiceProvider
{

    public function register()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('medma', MedmaFacade::class);

        $this->app->singleton('medma', function () {
            return new MediaManager();
        });

        $this->loadHelpers();
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'medma');
        $this->publishes([
            __DIR__.'/migrations/' => database_path('migrations')
        ], 'migrations');
        $this->publishes([
            __DIR__.'/resources/assets/css/app.scss' => resource_path('sass/mediamanager.scss'),
            __DIR__.'/resources/assets/js/app.js' => resource_path('js/mediamanager.js'),
        ], 'asset');
    }

    public function loadHelpers()
    {
        require_once(__DIR__.'/helpers.php');
    }
}