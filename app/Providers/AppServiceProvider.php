<?php

namespace App\Providers;

use App\Repo;
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
        $this->app->bind('\App\Repo',function($app){
            return new Repo($app->make('Repo'));
        });
    }
}
