<?php

namespace App\Providers;

use Illuminate\Foundation\Console\ServeCommand;
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
        $this->urlsPublicasEnCodespaces();
    }

    /**
     * Hace que las URLs absolutas apunten al dominio publico del codespace.
     *
     * Tu navegador entra por https://<codespace>-8000.app.github.dev, que por
     * fuera responde en el puerto 443. Dentro del contenedor el servidor escucha
     * en el 8000, y Laravel arma route(), url(), asset() y los redirect() con el
     * host, el esquema Y EL PUERTO de la peticion. Sin esto, el redirect de tu
     * store() sale como http://localhost:8000/ y el navegador no conecta.
     *
     * En el contenedor local no hace nada: ahi CODESPACE_NAME no existe.
     */
    private function urlsPublicasEnCodespaces(): void
    {
        // 'php artisan serve' NO atiende las peticiones en este proceso: levanta
        // el servidor en un proceso HIJO y solo le pasa una lista blanca de
        // variables de entorno. CODESPACE_NAME no esta en esa lista, asi que
        // dentro de las peticiones getenv() la ve vacia aunque en la terminal si
        // exista. Este boot() corre tambien en el proceso padre, antes de que
        // arranque el hijo, que es el momento justo para agregarla a la lista.
        if (class_exists(ServeCommand::class)) {
            foreach (['CODESPACE_NAME', 'GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN'] as $variable) {
                if (! in_array($variable, ServeCommand::$passthroughVariables)) {
                    ServeCommand::$passthroughVariables[] = $variable;
                }
            }
        }

        $raiz = null;

        if ($codespace = getenv('CODESPACE_NAME')) {
            $dominio = getenv('GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN') ?: 'app.github.dev';
            $raiz = "https://{$codespace}-8000.{$dominio}";
        } elseif (str_contains($appUrl = (string) config('app.url'), '.app.github.dev')) {
            // Respaldo: si la variable no llego pero el .env ya trae la direccion
            // publica, se usa esa. El .env si se lee dentro del proceso hijo,
            // porque sale de un archivo y no del entorno del proceso.
            $raiz = rtrim($appUrl, '/');
        }

        if ($raiz) {
            URL::forceRootUrl($raiz);

            // Sin esto las URLs salen en http dentro de una pagina https y el
            // navegador bloquea el envio del formulario por contenido mixto.
            URL::forceScheme('https');
        }
    }
}
