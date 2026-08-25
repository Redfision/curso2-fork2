<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // --- Las URLs cuando trabajas en GitHub Codespaces -------------------
        //
        // Tu navegador entra por el dominio publico del puerto 8000, pero la
        // peticion llega al contenedor como si viniera de localhost. Laravel
        // arma route(), url(), asset() y los redirect() con el host de la
        // peticion, asi que sin estas dos lineas el action de tu formulario
        // sale como http://localhost:8000/avisos y el navegador se va a una
        // direccion que no existe fuera del contenedor.
        //
        // Se configura solo, con las mismas variables que Codespaces ya define
        // y que tambien usa el vite.config.js del curso. En el contenedor local
        // no hace nada, porque ahi CODESPACE_NAME no existe.
        //
        // forceScheme('https') no es opcional: sin el las URLs salen en http
        // dentro de una pagina https y el navegador bloquea el envio del
        // formulario por contenido mixto.
        if ($codespace = env('CODESPACE_NAME')) {
            $dominio = env('GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN', 'app.github.dev');

            URL::forceRootUrl("https://{$codespace}-8000.{$dominio}");
            URL::forceScheme('https');
        }
    }
}
