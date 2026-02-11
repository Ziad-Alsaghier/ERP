<?php

namespace App\Providers;

use App\Models\ChMessage;
use App\Models\Utility;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

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
        Schema::defaultStringLength(191);
        
        try {
            if (Schema::hasTable('settings')) {
                View::share('setting', Utility::settings());
                View::share('languages', Utility::languages());
            }
        } catch (\Exception $e) {
            // Silently fail if settings table doesn't exist (e.g., during migrations)
        }
    }
    }


