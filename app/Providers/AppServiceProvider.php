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
        //
        // Se lee con getenv() y no con env(): env() pasa por el repositorio de
        // Dotenv, que se arma una sola vez al arrancar y que devuelve null si
        // alguien corrio 'php artisan config:cache'. getenv() lee el entorno
        // del proceso directo y no falla en ninguno de esos casos.
        if ($codespace = getenv('CODESPACE_NAME')) {
            $dominio = getenv('GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN') ?: 'app.github.dev';

            URL::forceRootUrl("https://{$codespace}-8000.{$dominio}");
            URL::forceScheme('https');
        }
    }
}
