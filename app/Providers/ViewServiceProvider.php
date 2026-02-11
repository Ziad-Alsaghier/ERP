<?php

namespace App\Providers;

use App\Models\ChMessage;
use App\Models\EmailTemplate;
use App\Models\Notification;
use App\Models\Plan;
use App\Models\Utility;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //

        View::composer('*', function ($view) {
                //$settings = Utility::settings();
                $view->with('logo', Utility::get_file('uploads/logo'));
                $view->with('meta_image', Utility::get_file('uploads/meta/'));
                $view->with('lang', App::getLocale('lang') == 'ar' ? 'on' : '');
                $view->with('emailTemplate',EmailTemplate::emailTemplateData());
            if(Auth::check()){
                $view->with('userPlan',Plan::getPlan(Auth::user()->show_dashboard()) );
                $view->with('user', Auth::user() );

                $view->with('unseenCounter',ChMessage::where('to_id', Auth::user()->id)->where('seen', 0)->count());
                $view->with('notifications',Notification::where('creator_id',Auth::user()->creatorId())->get());
            }
        });
    }
}
