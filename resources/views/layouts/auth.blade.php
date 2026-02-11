@extends('landingpage::layouts.landingpage')
{{-- @section('content')
@php
$settings_land = \Modules\LandingPage\Entities\LandingPageSetting::settings();
  use App\Models\Utility;

    $setting = Utility::settings();
    $company_logo = $setting['company_logo_dark'] ?? '';
    $company_logos = $setting['company_logo_light'] ?? '';
    $company_favicon = $setting['company_favicon'] ?? '';

    $logo = \App\Models\Utility::get_file('uploads/logo/');

    $color = !empty($setting['color']) ? $setting['color'] : 'theme-3';

    if(isset($setting['color_flag']) && $setting['color_flag'] == 'true')
    {
        $themeColor = 'custom-color';
    }
    else {
        $themeColor = $color;
    }
    $company_logo = \App\Models\Utility::GetLogo();
    $SITE_RTL = isset($setting['SITE_RTL']) ? $setting['SITE_RTL'] : 'off';
    $lang = \App::getLocale('lang');
    if ($lang == 'ar' || $lang == 'he') {
        $SITE_RTL = 'on';
    }
    elseif($SITE_RTL == 'on')
    {
        $SITE_RTL = 'on';
    }
    else {
        $SITE_RTL = 'on';
    }
    $metatitle = isset($setting['meta_title']) ? $setting['meta_title'] : '';
    $metsdesc = isset($setting['meta_desc']) ? $setting['meta_desc'] : '';
    $meta_image = \App\Models\Utility::get_file('uploads/meta/');
    $meta_logo = isset($setting['meta_image']) ? $setting['meta_image'] : '';
    $get_cookie = isset($setting['enable_cookie']) ? $setting['enable_cookie'] : '';
    $adminSettings = Utility::settings();
        if ($adminSettings['display_landing_page'] == 'on' && \Schema::hasTable('landing_page_settings')) {
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
        }
@endphp --}}

    @yield('content')

</html>
