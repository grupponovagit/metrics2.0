<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Configurazione Paginazione Custom
         * 
         * Utilizziamo le view custom DaisyUI + Tailwind per:
         * - Coerenza visuale con il tema
         * - Accessibilità (ARIA labels, focus rings)
         * - Responsività mobile-first
         * - i18n in italiano
         */
        Paginator::defaultView('vendor.pagination.tailwind-daisyui');
        Paginator::defaultSimpleView('vendor.pagination.simple-tailwind-daisyui');
    }
}
