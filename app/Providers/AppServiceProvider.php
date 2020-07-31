<?php

namespace App\Providers;

use App\Device;
use App\DeviceLog;
use App\Observers\DeviceLogObserver;
use App\Observers\DeviceObserver;
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

        Device::observe(DeviceObserver::class);
        DeviceLog::observe(DeviceLogObserver::class);
    }
}
