<?php

namespace Mbox\BackCore;

use Illuminate\Support\ServiceProvider;

class BackCoreServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Registrar las traducciones
        $this->loadTranslationsFrom(__DIR__.'/lang', 'backcore');

        // Opcional: permitir publicar con vendor:publish
        $this->publishes([
            __DIR__.'/lang' => resource_path('lang/vendor/backcore'),
        ], 'translations');
    }

    public function register()
    {
        //
    }
}