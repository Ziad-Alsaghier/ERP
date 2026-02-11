<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use  App\Models\Utility;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {

        if(\Auth::guard('web')->check())
        {
            return redirect()->route('home');
        }
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
        return view('auth.passwords.reset' , compact('adminSettings','settings','logo','sup_logo','metatitle','metsdesc','meta_image','meta_logo','get_cookie','setting','SITE_RTL','color','themeColor','request'));
    }

    /**
     * Handle an incoming new password request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {

        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]);
    }
}
