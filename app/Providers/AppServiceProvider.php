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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        if (config('app.debug')) {
            DB::listen(function ($query) {
                Log::debug('SQL QUERY', [
                    'sql' => $query->sql,
                    'time' => $query->time,
                    'bindings' => $query->bindings,
                ]);
            });

        }
        // if (file_exists(app_path('Helpers/helpers.php'))) {
        //     require_once app_path('Helpers/helpers.php');
        // }

        // if (file_exists(app_path('Helpers/LanguageHelper.php'))) {
        //     require_once app_path('Helpers/LanguageHelper.php');
        // }
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


