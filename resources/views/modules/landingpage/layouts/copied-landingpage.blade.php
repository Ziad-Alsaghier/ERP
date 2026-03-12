@php
$languages = \App\Models\Utility::languages();
if(\Auth::user() == null){
    $locale = Session::get('locale');
}else{
    $locale = App::getLocale();
}


if ($languages->has($locale)) {
    $LangName = $languages->get($locale);
} else {
    $LangName = '';
}

// عرض النتيجة



@endphp

<!DOCTYPE html>
<html lang="{{ $locale }}" 
 dir="{{  $locale == 'ar' ? 'rtl' : 'ltr' }}" 
>
<head>
    <title>{{ env('APP_NAME') }}</title>
    <!-- Meta -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta name="title" content="{{ $metatitle }}">
    <meta name="description" content="{{ $metsdesc }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ env('APP_URL') }}">
    <meta property="og:title" content="{{ $metatitle }}">
    <meta property="og:description" content="{{ $metsdesc }}">
    <meta property="og:image" content="{{ $meta_image . $meta_logo }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ env('APP_URL') }}">
    <meta property="twitter:title" content="{{ $metatitle }}">
    <meta property="twitter:description" content="{{ $metsdesc }}">
    <meta property="twitter:image" content="{{ $meta_image . $meta_logo }}">

    <!-- Favicon icon -->
    <link rel="icon" href="{{ $sup_logo . '/' . $adminSettings['company_favicon'] }}" type="image/x-icon" />
    <!-- font css -->
    <link rel="stylesheet" href=" {{ Module::asset('LandingPage:Resources/assets/fonts/tabler-icons.min.css') }}" />
    <link rel="stylesheet" href=" {{ Module::asset('LandingPage:Resources/assets/fonts/feather.css') }}" />
    <link rel="stylesheet" href="  {{ Module::asset('LandingPage:Resources/assets/fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ Module::asset('LandingPage:Resources/assets/fonts/material.css') }}" />
    <!-- vendor css -->
     @if ($locale == 'ar')
        <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}">
    @endif 
    @if ($setting['cust_darklayout'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css') }}">
    @else
        <link rel="stylesheet" href="{{ Module::asset('LandingPage:Resources/assets/css/style.css') }}"
            id="main-style-link">
    @endif

    <link rel="stylesheet" href=" {{ Module::asset('LandingPage:Resources/assets/css/customizer.css') }}" />
    <link rel="stylesheet" href=" {{ Module::asset('LandingPage:Resources/assets/css/landing-page.css') }}" />
    <link rel="stylesheet" href=" {{ Module::asset('LandingPage:Resources/assets/css/custom.css') }}" />

    <style>
        :root {
            --color-customColor: <?= $color ?>;    
        }
    </style>

    <link rel="stylesheet" href="{{ asset('css/custom-color.css') }}">
</head>

@if ($setting['cust_darklayout'] == 'on')
    <body class="{{ $themeColor }} landing-dark">
    @else
    <body class="{{ $themeColor }}">
@endif


{{-- header --}}
<header class="main-header ">
    @if ($settings['topbar_status'] == 'on')
        <div class="announcement bg-dark text-center p-2">
            <p class="mb-0">{!! $settings['topbar_notification_msg'] !!}</p>
        </div>
    @endif
    @if ($settings['menubar_status'] == 'on')
        <div class="container">
            <nav class="navbar navbar-expand-md  default top-nav-collapse">
                <div class="header-left">
                    <a class="navbar-brand bg-transparent" href="{{route('home.landingpage')}}">
                        <img src="{{ $logo . '/' . $settings['site_logo'] }}" alt="logo" class="landing_logo">
                    </a>
                </div>
                <div class="collapse navbar-collapse justify-content-center"  id="navbarTogglerDemo01">
                    <ul class="navbar-nav">
                        @if(isset($settings['home_title']))
                        <li class="nav-item">
                            <a class="nav-link active" href="#home">{{ $settings['home_title'] }}</a>
                        </li>
                        @endif
                        @if(isset($settings['plan_title']))
                        <li class="nav-item">
                            <a class="nav-link" href="#plan">{{ $settings['plan_title'] }}</a>
                        </li>
                        @endif
                        @if(isset($settings['faq_title']))
                        <li class="nav-item">
                            <a class="nav-link" href="#faq">{{ $settings['faq_title'] }}</a>
                        </li>
                        @endif

                        @if (is_array(json_decode($settings['menubar_page'])) || is_object(json_decode($settings['menubar_page'])))
                            @foreach (json_decode($settings['menubar_page']) as $key => $value)
                                @if ($value->header == 'on' && $value->template_name == 'page_content')
                                    <li class="nav-item">
                                        <a class="nav-link"
                                            href="{{ route('custom.page', $value->page_slug) }}">{{ __($value->menubar_page_name) }}</a>
                                    </li>
                                @elseif($value->header == 'on')
                                    <li class="nav-item">
                                        <a class="nav-link"
                                            href="{{ $value->page_url }}">{{ __($value->menubar_page_name) }}</a>
                                    </li>
                                @endif
                            @endforeach
                        @endif

                    </ul>
                    <button class="navbar-toggler bg-primary" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
                <div class="ms-auto d-flex justify-content-end gap-2">
           

                    @if (isset(\Auth::user()->name))
                    <a href="{{ route('home') }}" class="btn btn-dark ">
                        <span class="hide-mob me-2">
                            {{Auth::user()->name}}
                        </span>
                        <i data-feather="log-in"></i>
                    </a>
                        
                    @else
                    <a href="{{ route('login') }}" class="btn btn-dark ">
                        <span class="hide-mob me-2">
                            {{ __('Login') }}
                        </span>
                        <i data-feather="log-in"></i>
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-dark ">
                        <span class="hide-mob me-2">
                            {{ __('Register') }}
                        </span>
                        <i data-feather="user-check"></i>
                    </a>
                    @endif
                        {{-- Momen Done [2024-8-15 2:18AM] --}}
                        @if(\Auth::user() == null)
                        <div class="collapse navbar-collapse " id="navbarlogin">
                            <ul class="navbar-nav align-items-center ms-auto mb-2 mb-lg-0" style="justify-content: center;flex: auto;">
                                <div class="lang-dropdown-only-desk btn btn-outline-dark p-0">
                                    <li class="dropdown dash-h-item drp-language">
                                        <a class="dash-head-link dropdown-toggle btn" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-world nocolor pe-2"></i>
                                            <span class="drp-text pe-2">{{ucfirst($LangName)}}</span>
                                        </a>
                                        <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                                            @foreach ($languages as $code => $language)
                                                <a href="{{ route('change.language', $code) }}"
                                                class="dropdown-item {{ $locale == $code ? 'text-primary' : '' }}">
                                                    <span>{{ucFirst($language)}}</span>
                                                </a>
                                            @endforeach

                                            <h></h>

                                                @if(\Auth::user() !== null && \Auth::user()->type=='super admin')
                                                    <a  data-url="{{ route('create.language') }}" class="dropdown-item text-primary"  data-ajax-popup="true" data-title="{{__('Create New Language')}}">
                                                        {{ __('Create Language') }}
                                                    </a>
                                                    <a class="dropdown-item text-primary" href="{{route('manage.language',[isset($lang)?$lang:'english'])}}">{{ __('Manage Language') }}</a>
                                                @endif
                                        </div>
                                    </li>
                                </div>
                            </ul>
                        </div>
                    @else
                        @php
                        $users=\Auth::user();
                        $lang = isset($users->lang)?$users->lang:'en';
                            if ($lang == null) {
                                $lang = 'en';
                            }
                            $LangName = cache()->remember('full_language_data_' . $lang, now()->addHours(24), function () use ($lang) {
                                return \App\Models\Language::languageData($lang);
                            });
                        @endphp
                            <div class="collapse navbar-collapse " id="navbarlogin">
                                <ul class="navbar-nav align-items-center ms-auto mb-2 mb-lg-0" style="justify-content: center;flex: auto;">
                                    <div class="lang-dropdown-only-desk btn btn-outline-dark p-0">
                                        <li class="dropdown dash-h-item drp-language">
                                            <a class="dash-head-link dropdown-toggle btn" href="#" data-bs-toggle="dropdown" aria-expanded="false" >
                                                <i class="ti ti-world nocolor pe-2"></i>
                                                <span class="drp-text hide-mob pe-2">{{ucfirst($LangName->full_name)}}</span>
                                            </a>
                                            <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                                                @foreach ($languages as $code => $language)
                                                    <a href="{{ route('change.language', $code) }}"
                                                    class="dropdown-item {{ $lang == $code ? 'text-primary' : '' }}">
                                                        <span>{{ucFirst($language)}}</span>
                                                    </a>
                                                @endforeach
                        
                                                <h></h>
                                                    @if(\Auth::user()->type=='super admin')
                                                        <a  data-url="{{ route('create.language') }}" class="dropdown-item text-primary"  data-ajax-popup="true" data-title="{{__('Create New Language')}}">
                                                            {{ __('Create Language') }}
                                                        </a>
                                                        <a class="dropdown-item text-primary" href="{{route('manage.language',[isset($lang)?$lang:'english'])}}">{{ __('Manage Language') }}</a>
                                                    @endif
                                            </div>
                                        </li>
                                    </div>
                                </ul>
                            </div>
                    @endif

                    <button class="navbar-toggler " type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
            </nav>
        </div>
    @endif

</header>
{{-- end header --}}


@yield('content')

{{-- footer --}}
<footer class="site-footer bg-gray-100">
    <div class="container">
        <div class="footer-row">
            <div class="ftr-col cmp-detail">
                <div class="footer-logo mb-3">
                    <a href="#">
                        <img src="{{ $logo . '/' . $settings['site_logo'] }}" alt="logo">
                    </a>
                </div>
                <p>
                    {!! $settings['site_description'] !!}
                </p>

            </div>
            <div class="ftr-col">
                <ul class="list-unstyled">

                    @if (is_array(json_decode($settings['menubar_page'])) || is_object(json_decode($settings['menubar_page'])))
                        @foreach (json_decode($settings['menubar_page']) as $key => $value)
                            @if ($value->footer == 'on' && $value->header == 'off' && $value->template_name == 'page_content')
                                <li><a
                                        href="{{ route('custom.page', $value->page_slug) }}">{!! __($value->menubar_page_name) !!}</a>
                                </li>
                            @endif
                            @if ($value->footer == 'on' && $value->header == 'on' && $value->template_name == 'page_content')
                                <li><a
                                        href="{{ route('custom.page', $value->page_slug) }}">{!! __($value->menubar_page_name) !!}</a>
                                </li>
                            @endif
                            @if ($value->footer == 'on' && $value->header == 'on' && $value->template_name == 'page_url')
                                <li><a href="{{ $value->page_url }}">{!! __($value->menubar_page_name) !!}</a></li>
                            @endif
                            @if ($value->footer == 'on' && $value->header == 'off' && $value->template_name == 'page_url')
                                <li><a href="{{ $value->page_url }}">{!! __($value->menubar_page_name) !!}</a></li>
                            @endif
                        @endforeach
                    @endif

                </ul>
            </div>

            @if ($settings['joinus_status'] == 'on')
                <div class="ftr-col ftr-subscribe">
                    <h2>{!! $settings['joinus_heading'] !!}</h2>
                    <p>{!! $settings['joinus_description'] !!}</p>
                    <form method="post" action="{{ route('join_us_store') }}">
                        @csrf
                        <div class="input-wrapper border border-dark">
                            <input type="email" name="email" placeholder="Type your email address...">
                            <button type="submit" class="btn btn-dark rounded-pill">{{ __('Join Us') }}!</button>
                        </div>
                    </form>
                </div>
            @endif
                <div class="col">
                    <img src="{{asset('assets/images/visa&mastercard.png')}}" style="width:200px" alt="">
                </div>
        </div>
    </div>
    <div class="border-top border-dark text-center p-2">
        <p class="mb-0"> &copy;
            {{ date('Y') }}
            {{ Utility::getValByName('footer_text') ? Utility::getValByName('footer_text') : config('app.name', 'Roaa-tec') }}
        </p>

    </div>
</footer>
{{-- end footer --}}



<script src="{{ Module::asset('LandingPage:Resources/assets/js/plugins/popper.min.js') }}"></script>
<script src="{{ Module::asset('LandingPage:Resources/assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ Module::asset('LandingPage:Resources/assets/js/plugins/feather.min.js') }}"></script>

<script>
    // Start [ Menu hide/show on scroll ]
    let ost = 0;
    document.addEventListener("scroll", function() {
        let cOst = document.documentElement.scrollTop;
        if (cOst == 0) {
            document.querySelector(".navbar").classList.add("top-nav-collapse");
        } else if (cOst > ost) {
            document.querySelector(".navbar").classList.add("top-nav-collapse");
            document.querySelector(".navbar").classList.remove("default");
        } else {
            document.querySelector(".navbar").classList.add("default");
            document
                .querySelector(".navbar")
                .classList.remove("top-nav-collapse");
        }
        ost = cOst;
    });
    // End [ Menu hide/show on scroll ]

    var scrollSpy = new bootstrap.ScrollSpy(document.body, {
        target: "#navbar-example",
    });
    feather.replace();
</script>

<script src="{{ asset('js/jquery.min.js') }}"></script>

<script src="{{ asset('js/custom.js') }}"></script>

@if ($message = Session::get('success'))
<script>
    show_toastr('success', '{!! $message !!}');
</script>
@endif
@if ($message = Session::get('error'))
<script>
    show_toastr('error', '{!! $message !!}');
</script>
@endif


@if ($get_cookie['enable_cookie'] == 'on')
    @include('layouts.cookie_consent')
@endif

</body>

</html>
