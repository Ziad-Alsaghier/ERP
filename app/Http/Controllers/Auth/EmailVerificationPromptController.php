<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use  App\Models\Utility;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function __invoke(Request $request , $lang = '')
    {
        $adminSettings = Utility::settings();
        $settings = \Modules\LandingPage\Entities\LandingPageSetting::settings();
        $logo = Utility::get_file('uploads/landing_page_image');
        $sup_logo = Utility::get_file('uploads/logo');
        $metatitle = isset($adminSettings['meta_title']) ? $adminSettings['meta_title'] : '';
        $metsdesc = isset($adminSettings['meta_desc']) ? $adminSettings['meta_desc'] : '';
        $meta_image = Utility::get_file('uploads/meta/');
        $meta_logo = isset($adminSettings['meta_image']) ? $adminSettings['meta_image'] : '';
        $get_cookie = Utility::getCookieSetting();
        $setting = Utility::colorset();
        $SITE_RTL = $adminSettings['SITE_RTL'] ? $adminSettings['SITE_RTL'] : '';
        $color = !empty($setting['color']) ? $setting['color'] : 'theme-3';
        if(isset($setting['color_flag']) && $setting['color_flag'] == 'true')
        {
            $themeColor = 'custom-color';
        }
        else {
            $themeColor = $color;
        }
        return $request->user()->hasVerifiedEmail()  ? redirect()->intended(RouteServiceProvider::HOME)  : view('auth.verify' , compact('adminSettings','settings','logo','sup_logo','metatitle','metsdesc','meta_image','meta_logo','get_cookie','setting','SITE_RTL','color','themeColor'));
    }
}
