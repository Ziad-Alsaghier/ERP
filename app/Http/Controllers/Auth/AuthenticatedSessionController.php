<?php

namespace App\Http\Controllers\Auth;

use App\Models\Customer;
use App\Models\LoginDetail;
use App\Models\Plan;
use App\Models\Vender;
use  App\Models\Utility;
use  App\Models\User;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use DateTime;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */


    public function __construct()
    {
        // $this->middleware('guest')->except('logout');
    }

    public function create()
    {
        // return view('auth.login');
        $this->middleware('XSS');
    }



    public function store(LoginRequest $request)
    {


        $user = User::where('email',$request->email)->first();

        if($user != null)
        {
            $companyUser = User::where('id' , $user->created_by)->first();
        }

        if($user != null && $user->is_disable == 0 && $user->type != 'company' && $user->type != 'super admin')
        {
            return redirect()->back()->with('status', __('Your Account is disable,please contact your Administrator.'));
        }

        if(($user != null && $user->is_enable_login == 0 || (isset($companyUser) && $companyUser != null) && $companyUser->is_enable_login == 0)  && $user->type != 'super admin')
        {
            return redirect()->back()->with('status', __('Your Account is disable from company.'));
        }

        $settings = Utility::settings();
        //ReCpatcha

        if(isset($settings['recaptcha_module']) && $settings['recaptcha_module'] == 'on')
        {
            $validation['g-recaptcha-response'] = 'required|captcha';
        }else{
            $validation = [];
        }



        $this->validate($request, $validation);
        $request->authenticate();
        $request->session()->regenerate();
        $user = Auth::user();




        $companyUser = User::find($user->created_by);
        $status = $companyUser ? $companyUser->delete_status : 1;

        if($user->delete_status == 0 || $status == 0)
        {
            auth()->logout();
            return redirect()->back()->with('status', __('Your Account is deleted by admin,please contact your Administrator.'));
        }

        if($user->is_active == 0)
        {
            auth()->logout();
        }

        $user = \Auth::user();
        if($user->type == 'company')
        {
            $plan = Plan::find($user->plan);
            if($plan)
            {
                if($plan->duration != 'lifetime')
                {
                    $datetime1 = new \DateTime($user->plan_expire_date);
                    $datetime2 = new \DateTime(date('Y-m-d'));
                    //                    $interval  = $datetime1->diff($datetime2);
                    $interval = $datetime2->diff($datetime1);
                    $days     = $interval->format('%r%a');
                    if($days <= 0)
                    {
                        $user->assignPlan(1);

                        return redirect()->intended(RouteServiceProvider::HOME)->with('error', __('Your Plan is expired.'));
                    }
                }

                if($user->trial_expire_date != null)
                {
                    if(\Auth::user()->trial_expire_date < date('Y-m-d'))
                    {
                        $user->assignPlan(1);

                        return redirect()->intended(RouteServiceProvider::HOME)->with('error', __('Your Trial plan Expired.'));
                    }
                }
            }
        }

        $setting = Utility::settingsById($user->creatorId());

        $timezone = $setting['timezone'] ? $setting['timezone'] : 'UTC';
        date_default_timezone_set($timezone);

            // Update Last Login Time
            $user->update(
                [
                    'last_login_at' => Carbon::now()->toDateTimeString(),
                ]
            );
            // momen
            $accounts = User::where('email',$request->email)->get();
            if($accounts->count() > 1 ){

                return $this->chooseAccount($request);
            }
            // end momen
            $isAdminLogin = Auth::check() && Auth::user()->type === 'super admin';
            if (!$isAdminLogin) {
                if ($user) {
                    session()->put('user_id', $user->id);
                   $sessions = glob(storage_path('framework/sessions/*'));
                    foreach ($sessions as $session) {
                        $content = \Illuminate\Support\Facades\File::get($session); // Read session file
                        if (strpos($content, '"user_id";i:' . $user->id) !== false) {
                            \Illuminate\Support\Facades\File::delete($session); // Delete the session file
                        }
                    }
                }
            }
            // Featured: User Activity Log

        //start for user log
        if($user->type != 'company' && $user->type != 'super admin')
        {
            $isAdminLogin = Auth::check() && Auth::user()->type === 'super admin';
            if (!$isAdminLogin) {
                if ($user) {
                    session()->put('user_id', $user->id);
                 $sessions = glob(storage_path('framework/sessions/*'));
                    foreach ($sessions as $session) {
                        $content = \Illuminate\Support\Facades\File::get($session); // Read session file
                        if (strpos($content, '"user_id";i:' . $user->id) !== false) {
                            \Illuminate\Support\Facades\File::delete($session); // Delete the session file
                        }
                    }
                }
            }
            // Featured: User Activity Log

            $ip = '49.36.83.154'; // This is static ip address
            $ip = $_SERVER['REMOTE_ADDR']; // your ip address here
            $query = @unserialize(file_get_contents('http://ip-api.com/php/' . $ip));

            $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);
            if ($whichbrowser->device->type == 'bot') {
                return;
            }
            $referrer = isset($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER']) : null;

            /* Detect extra details about the user */
            $query['browser_name'] = $whichbrowser->browser->name ?? null;
            $query['os_name'] = $whichbrowser->os->name ?? null;
            $query['browser_language'] = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
            $query['device_type'] = get_device_type($_SERVER['HTTP_USER_AGENT']);
            $query['referrer_host'] = !empty($referrer['host']);
            $query['referrer_path'] = !empty($referrer['path']);

            isset($query['timezone'])?date_default_timezone_set($query['timezone']):'';

            $json = json_encode($query);

            $login_detail = new LoginDetail();
            $login_detail->user_id = Auth::user()->id;
            $login_detail->ip = $ip;
            $login_detail->date = date('Y-m-d H:i:s');
            $login_detail->Details = $json;
            $login_detail->created_by = \Auth::user()->creatorId();
            $login_detail->save();

    }
        //end for user log
        if($user->type =='company' || $user->type =='super admin' || $user->type =='client')
        {
            return redirect()->intended(RouteServiceProvider::HOME);

        }
        else
        {
            return redirect()->intended(RouteServiceProvider::EMPHOME);
        }
    }

    public static function chooseAccount(){


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
        $accounts = User::where('email',\Auth::user()->email)->get();
        if(isset($setting['color_flag']) && $setting['color_flag'] == 'true')
        {
            $themeColor = 'custom-color';
        }
        else {
            $themeColor = $color;
        }
        return view('auth.choose_account' , compact('adminSettings','settings','logo','sup_logo','metatitle','metsdesc','meta_image','meta_logo','get_cookie','setting','SITE_RTL','color','themeColor','accounts'));

    }

    public function destroy(Request $request)
    {
        session_start();
        unset($_SESSION['admin_ID']);
        unset($_SESSION['admin_Username']);
        session_destroy();
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showCustomerLoginForm($lang = '')
    {
        if($lang == '')
        {
            $lang = Utility::getValByName('default_language');
        }

        \App::setLocale($lang);

        return view('auth.customer_login', compact('lang'));
    }

    public function customerLogin(Request $request)
    {

        $this->validate(
            $request, [
                        'email' => 'required|email',
                        'password' => 'required|min:6',
                    ]
        );

        if(\Auth::guard('customer')->attempt(
            [
                'email' => $request->email,
                'password' => $request->password,
            ], $request->get('remember')
        ))
        {
            if(\Auth::guard('customer')->user()->is_active == 0)
            {
                \Auth::guard('customer')->logout();
            }
            $user = \Auth::guard('customer')->user();
            $user->update(
                [
                    'last_login_at' => Carbon::now()->toDateTimeString(),
                ]
            );

            return redirect()->route('customer.dashboard');
        }

        return $this->sendFailedLoginResponse(0);
    }

    public function showVenderLoginForm($lang = '')
    {
        if($lang == '')
        {
            $lang = Utility::getValByName('default_language');
        }

        \App::setLocale($lang);

        return view('auth.vender_login', compact('lang'));
    }

    public function venderLogin(Request $request)
    {
        $this->validate(
            $request, [
                        'email' => 'required|email',
                        'password' => 'required|min:6',
                    ]
        );
        if(\Auth::guard('vender')->attempt(
            [
                'email' => $request->email,
                'password' => $request->password,
            ], $request->get('remember')
        ))
        {
            if(\Auth::guard('vender')->user()->is_active == 0)
            {
                \Auth::guard('vender')->logout();
            }
            $user = \Auth::guard('vender')->user();
            $user->update(
                [
                    'last_login_at' => Carbon::now()->toDateTimeString(),
                ]
            );

            return redirect()->route('vender.dashboard');
        }

        return $this->sendFailedLoginResponse($request);
    }

    public function showLoginForm()
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
        return view('auth.login' , compact('adminSettings','settings','logo','sup_logo','metatitle','metsdesc','meta_image','meta_logo','get_cookie','setting','SITE_RTL','color','themeColor'));
    }

    public function showLinkRequestForm()
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
        return view('auth.forgot-password' , compact('adminSettings','settings','logo','sup_logo','metatitle','metsdesc','meta_image','meta_logo','get_cookie','setting','SITE_RTL','color','themeColor'));
    }

    public function showCustomerLoginLang($lang = '')
    {
        if($lang == '')
        {
            $lang = Utility::getValByName('default_language');
        }

        \App::setLocale($lang);

        return view('auth.customer_login', compact('lang'));
    }

    public function showVenderLoginLang($lang = '')
    {
        if($lang == '')
        {
            $lang = Utility::getValByName('default_language');
        }

        \App::setLocale($lang);

        return view('auth.vender_login', compact('lang'));
    }

    //    ---------------------------------Customer ----------------------------------_
    public function showCustomerLinkRequestForm($lang = '')
    {
        if($lang == '')
        {
            $lang = Utility::getValByName('default_language');
        }

        \App::setLocale($lang);

        return view('auth.passwords.customerEmail', compact('lang'));
    }

    public function postCustomerEmail(Request $request)
    {

        $request->validate(
            [
                'email' => 'required|email|exists:customers',
            ]
        );

        $token = \Str::random(60);

        DB::table('password_resets')->insert(
            [
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now(),
            ]
        );

        Mail::send(
            'auth.customerVerify', ['token' => $token], function ($message) use ($request){
            $message->from(env('MAIL_USERNAME'), env('MAIL_FROM_NAME'));
            $message->to($request->email);
            $message->subject('Reset Password Notification');
        }
        );

        return back()->with('status', 'We have e-mailed your password reset link!');
    }

    public function showResetForm(Request $request, $token = null)
    {

        $default_language = DB::table('settings')->select('value')->where('name', 'default_language')->first();
        $lang             = !empty($default_language) ? $default_language->value : 'en';

        \App::setLocale($lang);

        return view('auth.passwords.reset')->with(
            [
                'token' => $token,
                'email' => $request->email,
                'lang' => $lang,
            ]
        );
    }

    public function getCustomerPassword($token)
    {

        return view('auth.passwords.customerReset', ['token' => $token]);
    }

    public function updateCustomerPassword(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|email|exists:customers',
                'password' => 'required|string|min:6|confirmed',
                'password_confirmation' => 'required',

            ]
        );

        $updatePassword = DB::table('password_resets')->where(
            [
                'email' => $request->email,
                'token' => $request->token,
            ]
        )->first();

        if(!$updatePassword)
        {
            return back()->withInput()->with('error', 'Invalid token!');
        }

        $user = Customer::where('email', $request->email)->update(['password' => Hash::make($request->password)]);

        DB::table('password_resets')->where(['email' => $request->email])->delete();

        return redirect('/login')->with('message', 'Your password has been changed.');

    }

    //    ----------------------------Vendor----------------------------------------------------
    public function showVendorLinkRequestForm($lang = '')
    {
        if($lang == '')
        {
            $lang = Utility::getValByName('default_language');
        }

        \App::setLocale($lang);

        return view('auth.passwords.vendorEmail', compact('lang'));
    }

    public function postVendorEmail(Request $request)
    {

        $request->validate(
            [
                'email' => 'required|email|exists:venders',
            ]
        );

        $token = \Str::random(60);

        DB::table('password_resets')->insert(
            [
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now(),
            ]
        );

        Mail::send(
            'auth.vendorVerify', ['token' => $token], function ($message) use ($request){
            $message->from(env('MAIL_USERNAME'), env('MAIL_FROM_NAME'));
            $message->to($request->email);
            $message->subject('Reset Password Notification');
        }
        );

        return back()->with('status', 'We have e-mailed your password reset link!');
    }

    public function getVendorPassword($token)
    {

        return view('auth.passwords.vendorReset', ['token' => $token]);
    }

    public function updateVendorPassword(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|email|exists:venders',
                'password' => 'required|string|min:6|confirmed',
                'password_confirmation' => 'required',

            ]
        );

        $updatePassword = DB::table('password_resets')->where(
            [
                'email' => $request->email,
                'token' => $request->token,
            ]
        )->first();

        if(!$updatePassword)
        {
            return back()->withInput()->with('error', 'Invalid token!');
        }

        $user = Vender::where('email', $request->email)->update(['password' => Hash::make($request->password)]);

        DB::table('password_resets')->where(['email' => $request->email])->delete();

        return redirect('/login')->with('message', 'Your password has been changed.');

    }
}

//for user log
if (!function_exists('get_device_type')) {
    function get_device_type($user_agent)
    {
        $mobile_regex = '/(?:phone|windows\s+phone|ipod|blackberry|(?:android|bb\d+|meego|silk|googlebot) .+? mobile|palm|windows\s+ce|opera mini|avantgo|mobilesafari|docomo)/i';
        $tablet_regex = '/(?:ipad|playbook|(?:android|bb\d+|meego|silk)(?! .+? mobile))/i';
        if (preg_match_all($mobile_regex, $user_agent)) {
            return 'mobile';
        } else {
            if (preg_match_all($tablet_regex, $user_agent)) {
                return 'tablet';
            } else {
                return 'desktop';
            }
        }
    }
}
