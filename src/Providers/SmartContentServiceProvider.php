<?php namespace AvdS\SmartContent\Providers;

use Illuminate\Support\ServiceProvider;
use AvdS\SmartContent\Libs\SmartContentEngine;

class SmartContentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
        
        $this->app->bind('sc', function ($app) {
            
            return new SmartContentEngine(config('smartcontent'));
            
        });
            
    }
}
