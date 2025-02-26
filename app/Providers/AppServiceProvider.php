<?php

namespace App\Providers;

use App\Models\Configuracion;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
        if (Schema::hasTable('configuracions')) { // Verifica si la tabla existe
            $configuracion = Configuracion::first();

            if ($configuracion) {
                $defaultLogo = asset('vendor/adminlte/dist/img/logo.jpg');
                $logo = asset($configuracion->logo ?? $defaultLogo);
                $favicon = asset($configuracion->favicon ?? $defaultLogo);
                $loader = asset($configuracion->loader ?? $defaultLogo);

                Config::set('adminlte.title', $configuracion->nombre);
                Config::set('adminlte.logo', '<b>' . $configuracion->nombre . '</b>');
                Config::set('adminlte.logo_img', $logo);

                Config::set('adminlte.use_ico_only', false);
                Config::set('adminlte.use_full_favicon', true);

                Config::set('adminlte.preloader', [
                    'enabled' => true,
                    'mode' => 'fullscreen',
                    'img' => [
                        'path' => $loader,
                        'alt' => 'Cargando...',
                        'effect' => 'animation__shake',
                        'width' => 150,
                        'height' => 150,
                    ],
                ]);

                View::share('favicon', $favicon);
                View::share('loader', $loader);
            }
        }
    }
}
