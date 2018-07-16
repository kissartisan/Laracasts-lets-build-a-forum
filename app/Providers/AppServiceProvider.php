<?php

namespace App\Providers;

use App\Channel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        // The star "*" means share this variable with every single view
        \View::composer('*', function($view) {
            // Cache::forget('channels');
            $channels = Cache::rememberForever('channels', function() {
                return Channel::all();
            });

            $view->with('channels', $channels);
        });
        /** OR **/
        // \View::share('channels', Channel::all());
        // in Laravel 5.5 there is a make:rule command

        \Validator::extend('spamfree', 'App\Rules\SpamFree@passes');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal())
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
    }
}
