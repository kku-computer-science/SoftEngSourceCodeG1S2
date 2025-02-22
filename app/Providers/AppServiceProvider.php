<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (env("APP_ENV") === "production") {
            $this->app->bind('path.public', function () {
                return base_path() . '../public_html';
            });
        }
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        view()->composer(
            'layouts.layout',
            function ($view) {
                $view->with('dn', \App\Models\Program::where('degree_id', '=', 1)->get());
            }
        );
    }
}
