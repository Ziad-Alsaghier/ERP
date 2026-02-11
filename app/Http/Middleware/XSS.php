<?php

namespace App\Http\Middleware;

use App\Models\LandingPageSection;
use App\Models\User;
use App\Models\Utility;
use Closure;
use Config;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class XSS
{

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Session::has('locale')){
            App::setLocale(Session::get('locale'));
        }
        // dd(Session::get('locale'));

        if(\Auth::check())
        {
            $settings = Utility::settingsById(\Auth::user()->creatorId());
            if (!empty($settings['timezone'])) {
                Config::set('app.timezone', $settings['timezone']);
                date_default_timezone_set(Config::get('app.timezone', 'UTC'));
            }
            \App::setLocale(\Auth::user()->lang);
            
        }

        $input = $request->all();
        $request->merge($input);

        return $next($request);
    }
}
