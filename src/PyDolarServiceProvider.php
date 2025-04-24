<?php

namespace Darp5756\PyDolar;

use Illuminate\Support\ServiceProvider;

class PyDolarServiceProvider extends ServiceProvider
{
    /**
     * Registra cualquier servicio de aplicación.
	 *
     * @return void
     */
    public function register()
    {
        // Fusionar la configuración normal
        $this->mergeConfigFrom(
            __DIR__.'/../config/pydolar.php', 'pydolar',
        );

        // Fusionar la configuración de pruebas
        $this->mergeConfigFrom(
            __DIR__.'/../config/pydolar-test.php', 'pydolar-test',
        );
    }

    /**
     * Realiza cualquier inicialización de servicios después de que se hayan registrado.
	 *
     * @return void
     */
    public function boot()
    {
        // Publicar la configuración normal con la etiqueta 'pydolar-config'
        $this->publishes([
            __DIR__.'/../config/pydolar.php' => config_path('pydolar.php'),
        ], 'pydolar-config');

        // Publicar la configuración de pruebas con la etiqueta 'pydolar-test-config'
        $this->publishes([
            __DIR__.'/../config/pydolar_test.php' => config_path('pydolar_test.php'),
        ], 'pydolar-test-config');
    }
}
