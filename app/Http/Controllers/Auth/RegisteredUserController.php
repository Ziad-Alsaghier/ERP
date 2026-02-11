<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ExperienceCertificate;
use App\Models\GenerateOfferLetter;
use App\Models\JoiningLetter;
use App\Models\NOC;
use App\Models\User;
use  App\Models\Utility;
use Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */

  public function __construct()
    {
        $this->middleware('guest');
    }


    public function create()
    {
        // return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => ['required', 'string',
                'min:8','confirmed', Rules\Password::defaults()],
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'type' => 'company',
                'default_pipeline' => 1,
                'plan' => 0,
                'lang' => Utility::getValByName('default_language'),
                'avatar' => '',
                'created_by' => 1,
            ]);
            \Auth::login($user);
            $user->save();
            $role_r = Role::findByName('company');
            // config users
            $user->assignRole($role_r);
            $user->userDefaultDataRegister($user->id);
            $user->userDefaultBankAccount($user->id);
            Utility::chartOfAccountTypeData($user->id);
            Utility::chartOfAccountData1($user->id);
            GenerateOfferLetter::defaultOfferLetterRegister($user->id);
            ExperienceCertificate::defaultExpCertificatRegister($user->id);
            JoiningLetter::defaultJoiningLetterRegister($user->id);
            NOC::defaultNocCertificateRegister($user->id);

            $userArr = [
                'email' => $user->email,
                'password' => $request->password,
            ];
            Utility::sendUserEmailTemplate('new_user', [$user->id => $user->email], $userArr);

            return redirect(RouteServiceProvider::HOME);


    }

    public function showRegistrationForm()
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
        return view('auth.register' , compact('adminSettings','settings','logo','sup_logo','metatitle','metsdesc','meta_image','meta_logo','get_cookie','setting','SITE_RTL','color','themeColor'));
    }

}
